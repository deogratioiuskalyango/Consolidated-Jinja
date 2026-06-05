<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ShareClass;
use App\Models\ShareClassPermission;
use App\Models\ApprovalThreshold;
use App\Models\GovernanceRule;

class ShareClassSeeder extends Seeder
{
    public function run(): void
    {
        // Use first admin user as the owner; fall back to user id=1
        $owner = User::where('role', USER_ROLE_ADMIN)->first() ?? User::first();
        if (!$owner) {
            $this->command->error('No user found. Create a user first.');
            return;
        }
        $ownerId = $owner->id;

        // ── 6 Share Classes ───────────────────────────────────────────────────

        $classes = [

            // CLASS A — FOUNDER SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_A,
                'name'                              => 'Class A — Founder Shares',
                'type'                              => SHARE_CLASS_ORDINARY,
                'description'                       => 'Reserved for founders and controlling owners. Grants executive governance powers, super voting rights, and full financial oversight.',
                'par_value'                         => 1000.00,
                'voting_rights'                     => true,
                'voting_weight'                     => 10.0000,
                'voting_multiplier'                 => 10.0000,
                'dividend_rights'                   => true,
                'dividend_priority'                 => 1,
                'is_transferable'                   => false,
                'transfer_requires_board_approval'  => true,
                'transfer_requires_compliance_review' => true,
                'can_be_diluted_without_approval'   => false,
                'has_vesting'                       => false,
                'is_founder_class'                  => true,
                'governance_level'                  => GOVERNANCE_LEVEL_EXECUTIVE,
                'financial_approval_limit'          => null,  // unlimited
                'max_ownership_cap'                 => null,
                'fixed_dividend_rate'               => null,
                'dividend_cumulative'               => false,
                'allowed_resolution_types'          => null,  // can vote on all
                'governance_notes'                  => 'Founder shares. 1 share = 10 votes. Cannot be diluted without founder consent.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => true,  'limit_value' => null],    // unlimited
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => true,  'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => true,  'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => true,  'limit_value' => null],
                ],
            ],

            // CLASS B — ORDINARY SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_B,
                'name'                              => 'Class B — Ordinary Shares',
                'type'                              => SHARE_CLASS_ORDINARY,
                'description'                       => 'Standard investors and active shareholders with normal voting rights, dividend participation, and governance involvement.',
                'par_value'                         => 100.00,
                'voting_rights'                     => true,
                'voting_weight'                     => 1.0000,
                'voting_multiplier'                 => 1.0000,
                'dividend_rights'                   => true,
                'dividend_priority'                 => 5,
                'is_transferable'                   => true,
                'transfer_requires_board_approval'  => false,
                'transfer_requires_compliance_review' => false,
                'can_be_diluted_without_approval'   => true,
                'has_vesting'                       => false,
                'is_founder_class'                  => false,
                'governance_level'                  => GOVERNANCE_LEVEL_ENHANCED,
                'financial_approval_limit'          => 500000.00,  // KES 500,000
                'max_ownership_cap'                 => 49.00,      // 49%
                'fixed_dividend_rate'               => null,
                'dividend_cumulative'               => false,
                'allowed_resolution_types'          => null,       // all types
                'governance_notes'                  => 'Standard shares. 1 share = 1 vote. Cannot override founder decisions.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => true,  'limit_value' => 500000.00],
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => true,  'limit_value' => null],
                ],
            ],

            // CLASS C — PREFERENCE SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_C,
                'name'                              => 'Class C — Preference Shares',
                'type'                              => SHARE_CLASS_PREFERENCE,
                'description'                       => 'Passive or financial investors with priority dividend payments, capital protection, and limited governance participation.',
                'par_value'                         => 500.00,
                'voting_rights'                     => true,    // limited
                'voting_weight'                     => 0.5000,
                'voting_multiplier'                 => 0.5000,  // half vote weight
                'dividend_rights'                   => true,
                'dividend_priority'                 => 2,       // paid before ordinary
                'is_transferable'                   => true,
                'transfer_requires_board_approval'  => false,
                'transfer_requires_compliance_review' => false,
                'can_be_diluted_without_approval'   => true,
                'has_vesting'                       => false,
                'is_founder_class'                  => false,
                'governance_level'                  => GOVERNANCE_LEVEL_BASIC,
                'financial_approval_limit'          => 0.00,    // cannot approve
                'max_ownership_cap'                 => null,
                'fixed_dividend_rate'               => 8.00,   // 8% fixed
                'dividend_cumulative'               => true,
                'allowed_resolution_types'          => [RESOLUTION_TYPE_DIVIDEND, RESOLUTION_TYPE_OTHER],
                'governance_notes'                  => 'Preference shares. Fixed 8% dividend paid before ordinary. Limited to dividend & liquidation votes only.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => true,  'limit_value' => null],  // limited by allowed_resolution_types
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => true,  'limit_value' => null],
                ],
            ],

            // CLASS D — NON-VOTING SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_D,
                'name'                              => 'Class D — Non-Voting Shares',
                'type'                              => SHARE_CLASS_DEFERRED,
                'description'                       => 'Silent investors or family ownership. Full dividend rights but no voting or governance participation.',
                'par_value'                         => 100.00,
                'voting_rights'                     => false,
                'voting_weight'                     => 0.0000,
                'voting_multiplier'                 => 0.0000,
                'dividend_rights'                   => true,
                'dividend_priority'                 => 5,
                'is_transferable'                   => true,
                'transfer_requires_board_approval'  => false,
                'transfer_requires_compliance_review' => false,
                'can_be_diluted_without_approval'   => true,
                'has_vesting'                       => false,
                'is_founder_class'                  => false,
                'governance_level'                  => GOVERNANCE_LEVEL_BASIC,
                'financial_approval_limit'          => 0.00,
                'max_ownership_cap'                 => null,
                'fixed_dividend_rate'               => null,
                'dividend_cumulative'               => false,
                'allowed_resolution_types'          => [],  // no votes
                'governance_notes'                  => 'Non-voting shares. Informational access only. No governance participation.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => false, 'limit_value' => null],
                ],
            ],

            // CLASS E — EMPLOYEE / ESOP SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_E,
                'name'                              => 'Class E — Employee / ESOP Shares',
                'type'                              => SHARE_CLASS_ORDINARY,
                'description'                       => 'Employee Stock Ownership Plan shares. Vesting-based ownership with limited governance rights and employment-linked restrictions.',
                'par_value'                         => 50.00,
                'voting_rights'                     => false,
                'voting_weight'                     => 0.0000,
                'voting_multiplier'                 => 0.0000,
                'dividend_rights'                   => true,   // conditional on vesting
                'dividend_priority'                 => 8,
                'is_transferable'                   => false,  // cannot transfer externally
                'transfer_requires_board_approval'  => true,
                'transfer_requires_compliance_review' => false,
                'can_be_diluted_without_approval'   => true,
                'has_vesting'                       => true,
                'is_founder_class'                  => false,
                'governance_level'                  => GOVERNANCE_LEVEL_BASIC,
                'financial_approval_limit'          => 0.00,
                'max_ownership_cap'                 => 5.00,   // max 5% any one employee
                'fixed_dividend_rate'               => null,
                'dividend_cumulative'               => false,
                'allowed_resolution_types'          => [],     // no governance votes
                'governance_notes'                  => 'ESOP shares. Employment-linked. Auto-forfeiture on resignation. Cannot be transferred externally.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => false, 'limit_value' => null],
                ],
            ],

            // CLASS F — STRATEGIC INVESTOR SHARES
            [
                'class_code'                        => SHARE_CLASS_CODE_F,
                'name'                              => 'Class F — Strategic Investor Shares',
                'type'                              => SHARE_CLASS_ORDINARY,
                'description'                       => 'Institutional investors and strategic partners with enhanced financial oversight, weighted voting on acquisitions and expansion.',
                'par_value'                         => 1000.00,
                'voting_rights'                     => true,
                'voting_weight'                     => 2.0000,
                'voting_multiplier'                 => 2.0000,  // 2x on strategic votes
                'dividend_rights'                   => true,
                'dividend_priority'                 => 3,
                'is_transferable'                   => true,
                'transfer_requires_board_approval'  => false,
                'transfer_requires_compliance_review' => true,
                'can_be_diluted_without_approval'   => false,
                'has_vesting'                       => false,
                'is_founder_class'                  => false,
                'governance_level'                  => GOVERNANCE_LEVEL_ENHANCED,
                'financial_approval_limit'          => null,    // unlimited for high-value investments
                'max_ownership_cap'                 => 30.00,   // 30% cap
                'fixed_dividend_rate'               => null,
                'dividend_cumulative'               => false,
                'allowed_resolution_types'          => null,    // can vote on all
                'governance_notes'                  => 'Strategic investor shares. 1 share = 2 votes. Compliance review required for transfers.',
                'status'                            => ACTIVE,
                'permissions' => [
                    PERM_VOTE                    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_EXPENSES        => ['is_allowed' => true,  'limit_value' => null],   // high-value only (no limit stored; threshold rules apply)
                    PERM_VIEW_FINANCIAL_REPORTS  => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPROVE_ACQUISITIONS    => ['is_allowed' => true,  'limit_value' => null],
                    PERM_APPOINT_DIRECTORS       => ['is_allowed' => true,  'limit_value' => null],   // limited — with A
                    PERM_ACCESS_AUDIT_LOGS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_RECEIVE_DIVIDENDS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_BOARD_REPORTS      => ['is_allowed' => true,  'limit_value' => null],
                    PERM_CREATE_RESOLUTIONS      => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VETO_RESOLUTIONS        => ['is_allowed' => false, 'limit_value' => null],
                    PERM_TRIGGER_EMERGENCY_VOTE  => ['is_allowed' => false, 'limit_value' => null],
                    PERM_APPROVE_SHARE_TRANSFERS => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ONBOARD_SHAREHOLDERS    => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_ANALYTICS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_ACCESS_CONFIDENTIAL     => ['is_allowed' => false, 'limit_value' => null],
                    PERM_ACCESS_RISK_REPORTS     => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_ACQUISITIONS       => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_DIVIDENDS          => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VOTE_LIQUIDATION        => ['is_allowed' => true,  'limit_value' => null],
                    PERM_VIEW_ESOP               => ['is_allowed' => false, 'limit_value' => null],
                    PERM_VIEW_INVESTMENT         => ['is_allowed' => true,  'limit_value' => null],
                ],
            ],
        ];

        // ── Insert/update share classes and their permissions ─────────────────

        foreach ($classes as $classData) {
            $permissions = $classData['permissions'];
            unset($classData['permissions']);

            $classData['owner_user_id'] = $ownerId;

            // Update if code+owner already exists, else create
            $shareClass = ShareClass::updateOrCreate(
                ['owner_user_id' => $ownerId, 'class_code' => $classData['class_code']],
                $classData
            );

            // Upsert permissions
            foreach ($permissions as $key => $permData) {
                ShareClassPermission::updateOrCreate(
                    ['share_class_id' => $shareClass->id, 'permission_key' => $key],
                    ['is_allowed' => $permData['is_allowed'], 'limit_value' => $permData['limit_value']]
                );
            }

            $this->command->info("✔ {$shareClass->class_code} — {$shareClass->name} seeded with " . count($permissions) . " permissions.");
        }

        // ── Default Approval Thresholds ───────────────────────────────────────

        $thresholds = [
            [
                'name'                       => 'Low-Level Expenses',
                'min_amount'                 => 0,
                'max_amount'                 => 99999.99,
                'required_approver_classes'  => [SHARE_CLASS_CODE_B],
                'min_approver_count'         => 1,
                'required_ownership_percentage' => null,
                'requires_quorum'            => false,
                'quorum_percentage'          => null,
                'description'               => 'Expenses up to KES 100,000 can be approved by any Class B (Ordinary) shareholder.',
                'is_active'                  => true,
            ],
            [
                'name'                       => 'Medium-Level Expenses',
                'min_amount'                 => 100000,
                'max_amount'                 => 4999999.99,
                'required_approver_classes'  => [SHARE_CLASS_CODE_A, SHARE_CLASS_CODE_B],
                'min_approver_count'         => 2,
                'required_ownership_percentage' => null,
                'requires_quorum'            => true,
                'quorum_percentage'          => 30.00,
                'description'               => 'Expenses KES 100K–5M require approval from both a Class A and Class B shareholder, with 30% quorum.',
                'is_active'                  => true,
            ],
            [
                'name'                       => 'Major Investments',
                'min_amount'                 => 5000000,
                'max_amount'                 => null,
                'required_approver_classes'  => [SHARE_CLASS_CODE_A, SHARE_CLASS_CODE_F],
                'min_approver_count'         => 2,
                'required_ownership_percentage' => 51.00,
                'requires_quorum'            => true,
                'quorum_percentage'          => 51.00,
                'description'               => 'Major investments above KES 5M require Founder + Strategic Investor approval, 51% ownership quorum.',
                'is_active'                  => true,
            ],
        ];

        foreach ($thresholds as $threshold) {
            $threshold['owner_user_id'] = $ownerId;
            ApprovalThreshold::updateOrCreate(
                ['owner_user_id' => $ownerId, 'name' => $threshold['name']],
                $threshold
            );
            $this->command->info("✔ Threshold: {$threshold['name']} seeded.");
        }

        // ── Default Governance Rules ──────────────────────────────────────────

        $rules = [
            [
                'rule_key'    => 'quorum_percentage_default',
                'rule_name'   => 'Default Quorum Percentage',
                'rule_type'   => 'voting',
                'description' => 'Minimum ownership percentage required for a vote to be valid.',
                'config'      => ['percentage' => 25.0],
                'is_active'   => true,
            ],
            [
                'rule_key'    => 'founder_veto_enabled',
                'rule_name'   => 'Founder Veto Rights',
                'rule_type'   => 'voting',
                'description' => 'Whether Class A shareholders can veto strategic resolutions.',
                'config'      => ['enabled' => true, 'applies_to_types' => [RESOLUTION_TYPE_ACQUISITION, RESOLUTION_TYPE_DIRECTOR]],
                'is_active'   => true,
            ],
            [
                'rule_key'    => 'weighted_voting_enabled',
                'rule_name'   => 'Weighted Voting',
                'rule_type'   => 'voting',
                'description' => 'Whether voting weight is calculated using share class multiplier × shares held.',
                'config'      => ['enabled' => true],
                'is_active'   => true,
            ],
        ];

        foreach ($rules as $rule) {
            $rule['owner_user_id'] = $ownerId;
            GovernanceRule::updateOrCreate(
                ['owner_user_id' => $ownerId, 'rule_key' => $rule['rule_key']],
                $rule
            );
            $this->command->info("✔ Rule: {$rule['rule_name']} seeded.");
        }

        $this->command->newLine();
        $this->command->info('Share Class Governance Engine seeded successfully.');
    }
}
