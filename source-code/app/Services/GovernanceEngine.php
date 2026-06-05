<?php

namespace App\Services;

use App\Models\Shareholder;
use App\Models\Resolution;
use App\Models\FinancialApproval;
use App\Models\ApprovalThreshold;
use App\Models\GovernanceRule;
use App\Models\VoteDelegation;

/**
 * GovernanceEngine — Central authority for all governance permission checks.
 *
 * Usage (from controllers / middleware):
 *   app(GovernanceEngine::class)->canVote($shareholder, $resolution)
 *   app(GovernanceEngine::class)->hasPermission($shareholder, PERM_ACCESS_AUDIT_LOGS)
 */
class GovernanceEngine
{
    // ── Voting ───────────────────────────────────────────────────────────────

    /**
     * Can this shareholder cast a vote on this specific resolution?
     */
    public function canVote(Shareholder $shareholder, Resolution $resolution): bool
    {
        if ($shareholder->status !== SHAREHOLDER_STATUS_ACTIVE) return false;

        $class = $shareholder->relationLoaded('shareClass') ? $shareholder->shareClass : $shareholder->load('shareClass')->shareClass;
        if (!$class || !$class->voting_rights) return false;

        if (!$this->hasPermission($shareholder, PERM_VOTE)) return false;

        // Check if resolution type is allowed for this share class
        return $shareholder->canVoteOnResolutionType($resolution->type ?? RESOLUTION_TYPE_OTHER);
    }

    /**
     * Get the effective voting weight for a shareholder (shares × class multiplier).
     */
    public function getVotingWeight(Shareholder $shareholder): float
    {
        return $shareholder->effective_voting_weight;
    }

    /**
     * Get delegated votes incoming to a shareholder for a specific resolution.
     * Returns total delegated weight.
     */
    public function getDelegatedWeight(Shareholder $shareholder, Resolution $resolution): float
    {
        $delegations = VoteDelegation::with('delegator.shareClass')
            ->where('delegatee_shareholder_id', $shareholder->id)
            ->where('is_active', true)
            ->where('valid_from', '<=', now()->toDateString())
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()->toDateString()))
            ->where(fn($q) => $q->whereNull('resolution_id')->orWhere('resolution_id', $resolution->id))
            ->get();

        return $delegations->sum(fn($d) => $this->getVotingWeight($d->delegator));
    }

    /**
     * Compute weighted vote totals for a resolution.
     */
    public function computeWeightedResults(Resolution $resolution): array
    {
        $votes = $resolution->votes()->with('shareholder.shareClass')->get();

        $totalWeight = 0.0;
        $forWeight   = 0.0;
        $against     = 0.0;
        $abstain     = 0.0;

        foreach ($votes as $vote) {
            // Use stored weight if present, else recompute
            $w = (float) ($vote->voting_weight ?: $this->getVotingWeight($vote->shareholder));
            $totalWeight += $w;
            match ((int) $vote->vote) {
                VOTE_FOR     => ($forWeight += $w),
                VOTE_AGAINST => ($against   += $w),
                VOTE_ABSTAIN => ($abstain   += $w),
                default      => null,
            };
        }

        $safe = fn($n) => $totalWeight > 0 ? round($n / $totalWeight * 100, 2) : 0.0;

        return [
            'total_weight'       => $totalWeight,
            'for_weight'         => $forWeight,
            'against_weight'     => $against,
            'abstain_weight'     => $abstain,
            'for_percentage'     => $safe($forWeight),
            'against_percentage' => $safe($against),
            'abstain_percentage' => $safe($abstain),
        ];
    }

    // ── Permissions ──────────────────────────────────────────────────────────

    /**
     * Check if a shareholder holds a specific permission via their share class.
     */
    public function hasPermission(Shareholder $shareholder, string $permissionKey): bool
    {
        if ($shareholder->status !== SHAREHOLDER_STATUS_ACTIVE) return false;

        $class = $shareholder->relationLoaded('shareClass')
            ? $shareholder->shareClass
            : $shareholder->load('shareClass.permissions')->shareClass;

        if (!$class) return false;

        if (!$class->relationLoaded('permissions')) {
            $class->load('permissions');
        }

        $perm = $class->permissions->firstWhere('permission_key', $permissionKey);
        return $perm ? (bool) $perm->is_allowed : false;
    }

    /**
     * Get limit value for a permission (e.g. max expense amount).
     * Returns null if unlimited.
     */
    public function getPermissionLimit(Shareholder $shareholder, string $permissionKey): ?float
    {
        $class = $shareholder->load('shareClass.permissions')->shareClass;
        if (!$class) return 0.0;
        $perm = $class->permissions->firstWhere('permission_key', $permissionKey);
        return $perm ? $perm->limit_value : 0.0;
    }

    // ── Financial Approvals ──────────────────────────────────────────────────

    /**
     * Can this shareholder act on a financial approval of the given amount?
     */
    public function canApproveExpense(Shareholder $shareholder, float $amount): bool
    {
        if (!$this->hasPermission($shareholder, PERM_APPROVE_EXPENSES)) return false;

        $limit = $this->getPermissionLimit($shareholder, PERM_APPROVE_EXPENSES);
        return $limit === null || $amount <= $limit;
    }

    /**
     * Get the applicable approval threshold rule for an expense amount.
     */
    public function getApplicableThreshold(int $ownerUserId, float $amount): ?ApprovalThreshold
    {
        return ApprovalThreshold::where('owner_user_id', $ownerUserId)
            ->where('is_active', true)
            ->where('min_amount', '<=', $amount)
            ->where(fn($q) => $q->whereNull('max_amount')->orWhere('max_amount', '>=', $amount))
            ->orderByDesc('min_amount')
            ->first();
    }

    /**
     * Get all approval thresholds for an owner, ordered by amount.
     */
    public function getThresholds(int $ownerUserId): \Illuminate\Database\Eloquent\Collection
    {
        return ApprovalThreshold::where('owner_user_id', $ownerUserId)
            ->where('is_active', true)
            ->orderBy('min_amount')
            ->get();
    }

    // ── Share Transfers ──────────────────────────────────────────────────────

    /**
     * Can a shareholder initiate a transfer of their shares?
     */
    public function canTransferShares(Shareholder $shareholder): bool
    {
        $class = $shareholder->shareClass;
        return $class && $class->is_transferable;
    }

    /**
     * Does this transfer require board approval?
     */
    public function transferRequiresBoardApproval(Shareholder $shareholder): bool
    {
        return (bool) $shareholder->shareClass?->transfer_requires_board_approval;
    }

    /**
     * Does this transfer require compliance review?
     */
    public function transferRequiresComplianceReview(Shareholder $shareholder): bool
    {
        return (bool) $shareholder->shareClass?->transfer_requires_compliance_review;
    }

    // ── Governance Rules ────────────────────────────────────────────────────

    /**
     * Get a specific governance rule config value.
     */
    public function getRule(int $ownerUserId, string $ruleKey, $default = null)
    {
        $rule = GovernanceRule::where('owner_user_id', $ownerUserId)
            ->where('rule_key', $ruleKey)
            ->where('is_active', true)
            ->first();

        return $rule ? $rule->config : $default;
    }

    // ── Dashboard Rights Summary ─────────────────────────────────────────────

    /**
     * Return a structured summary of all governance rights for a shareholder.
     * Used by the shareholder dashboard and governance-rights page.
     */
    public function getRightsSummary(Shareholder $shareholder): array
    {
        $shareholder->loadMissing('shareClass.permissions');
        $class = $shareholder->shareClass;

        $allPerms = [
            PERM_VOTE, PERM_APPROVE_EXPENSES, PERM_VIEW_FINANCIAL_REPORTS,
            PERM_APPROVE_ACQUISITIONS, PERM_APPOINT_DIRECTORS, PERM_ACCESS_AUDIT_LOGS,
            PERM_RECEIVE_DIVIDENDS, PERM_VIEW_BOARD_REPORTS, PERM_CREATE_RESOLUTIONS,
            PERM_VETO_RESOLUTIONS, PERM_TRIGGER_EMERGENCY_VOTE, PERM_APPROVE_SHARE_TRANSFERS,
            PERM_ONBOARD_SHAREHOLDERS, PERM_VIEW_ANALYTICS, PERM_ACCESS_CONFIDENTIAL,
            PERM_ACCESS_RISK_REPORTS, PERM_VOTE_ACQUISITIONS, PERM_VOTE_DIVIDENDS,
            PERM_VOTE_LIQUIDATION, PERM_VIEW_ESOP, PERM_VIEW_INVESTMENT,
        ];

        $permMap    = [];
        $limitMap   = [];
        foreach ($allPerms as $perm) {
            $permMap[$perm]  = $this->hasPermission($shareholder, $perm);
            $limit           = $this->getPermissionLimit($shareholder, $perm);
            if ($limit !== null && $limit > 0) {
                $limitMap[$perm] = $limit;
            }
        }

        $effectiveWeight = $this->getVotingWeight($shareholder);

        // Vesting schedule summary (only for active ESOP shareholders)
        $vestingSummary = null;
        if ($class?->has_vesting) {
            $vs = $shareholder->vestingSchedules()->where('is_active', true)->first();
            if ($vs) {
                $vestingSummary = [
                    'total_granted'      => $vs->total_shares_granted,
                    'shares_vested'      => $vs->shares_vested,
                    'shares_available'   => $vs->sharesAvailable,
                    'progress_percent'   => $vs->vestingProgressPercent,
                    'cliff_date'         => $vs->cliff_date?->format('d M Y'),
                    'vesting_end_date'   => $vs->vesting_end_date?->format('d M Y'),
                    'employment_linked'  => $vs->employment_linked,
                ];
            }
        }

        return [
            // Identity
            'shareholder_id'        => $shareholder->id,
            'total_shares'          => $shareholder->total_shares,
            'ownership_percentage'  => $shareholder->ownership_percentage,
            'effective_voting_weight' => $effectiveWeight,

            // Share class (nested for convenient view access)
            'share_class' => [
                'name'                           => $class?->name,
                'class_code'                     => $class?->class_code,
                'voting_multiplier'              => $class?->voting_multiplier ?? 1,
                'dividend_priority'              => $class?->dividend_priority ?? 10,
                'fixed_dividend_rate'            => $class?->fixed_dividend_rate,
                'max_ownership_cap'              => $class?->max_ownership_cap,
                'financial_approval_limit'       => $class?->financial_approval_limit,
                'transfer_restrictions_label'    => $class?->transfer_restrictions_label,
                'is_founder_class'               => $class?->is_founder_class ?? false,
                'has_vesting'                    => $class?->has_vesting ?? false,
            ],

            // Shortcuts
            'class_code_badge'      => $class?->class_code_badge,
            'governance_level'      => $class?->governance_level,
            'governance_level_label' => $class?->governance_level_label,
            'can_vote'              => $permMap[PERM_VOTE] ?? false,
            'can_transfer_shares'   => $this->canTransferShares($shareholder),
            'has_vesting'           => $class?->has_vesting ?? false,
            'is_founder_class'      => $class?->is_founder_class ?? false,
            'financial_approval_limit' => $class?->financial_approval_limit,

            // Permission maps
            'permissions'           => $permMap,
            'permission_limits'     => $limitMap,

            // Vesting schedule data (null if not ESOP)
            'vesting_schedule'      => $vestingSummary,
        ];
    }
}
