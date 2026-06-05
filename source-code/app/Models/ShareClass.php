<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShareClass extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'voting_rights'                    => 'boolean',
        'dividend_rights'                  => 'boolean',
        'is_transferable'                  => 'boolean',
        'transfer_requires_board_approval' => 'boolean',
        'transfer_requires_compliance_review' => 'boolean',
        'can_be_diluted_without_approval'  => 'boolean',
        'has_vesting'                      => 'boolean',
        'is_founder_class'                 => 'boolean',
        'dividend_cumulative'              => 'boolean',
        'allowed_resolution_types'         => 'array',
    ];

    public function shareholders()
    {
        return $this->hasMany(Shareholder::class);
    }

    public function permissions()
    {
        return $this->hasMany(ShareClassPermission::class);
    }

    // ── Convenience permission checker ──────────────────────────────────────

    public function hasPermission(string $key): bool
    {
        return (bool) $this->permissions->where('permission_key', $key)->first()?->is_allowed;
    }

    public function getPermissionLimit(string $key): ?float
    {
        return $this->permissions->where('permission_key', $key)->first()?->limit_value;
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getGovernanceLevelLabelAttribute(): string
    {
        return match ((int) $this->governance_level) {
            GOVERNANCE_LEVEL_EXECUTIVE => 'Executive',
            GOVERNANCE_LEVEL_ENHANCED  => 'Enhanced',
            default                    => 'Basic',
        };
    }

    public function getClassCodeBadgeAttribute(): string
    {
        $colours = [
            'A' => '#7B2FBE', // purple — Founder
            'B' => '#2563EB', // blue — Ordinary
            'C' => '#059669', // green — Preference
            'D' => '#6B7280', // gray — Non-Voting
            'E' => '#D97706', // amber — Employee
            'F' => '#DC2626', // red — Strategic
        ];
        $colour = $colours[$this->class_code] ?? '#374151';
        return sprintf(
            '<span style="background:%s;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">%s</span>',
            $colour, $this->class_code ?? '—'
        );
    }

    public function getTransferRestrictionsLabelAttribute(): string
    {
        if (!$this->is_transferable) return 'Not Transferable';
        if ($this->transfer_requires_board_approval && $this->transfer_requires_compliance_review) return 'Board + Compliance';
        if ($this->transfer_requires_board_approval) return 'Board Approval';
        if ($this->transfer_requires_compliance_review) return 'Compliance Review';
        return 'Free Transfer';
    }
}
