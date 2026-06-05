<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareClass;
use App\Models\ShareClassPermission;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ShareClassController extends Controller
{
    use ResponseTrait;

    // All defined permission keys
    private array $allPermissions = [
        PERM_VOTE, PERM_APPROVE_EXPENSES, PERM_VIEW_FINANCIAL_REPORTS,
        PERM_APPROVE_ACQUISITIONS, PERM_APPOINT_DIRECTORS, PERM_ACCESS_AUDIT_LOGS,
        PERM_RECEIVE_DIVIDENDS, PERM_VIEW_BOARD_REPORTS, PERM_CREATE_RESOLUTIONS,
        PERM_VETO_RESOLUTIONS, PERM_TRIGGER_EMERGENCY_VOTE, PERM_APPROVE_SHARE_TRANSFERS,
        PERM_ONBOARD_SHAREHOLDERS, PERM_VIEW_ANALYTICS, PERM_ACCESS_CONFIDENTIAL,
        PERM_ACCESS_RISK_REPORTS, PERM_VOTE_ACQUISITIONS, PERM_VOTE_DIVIDENDS,
        PERM_VOTE_LIQUIDATION, PERM_VIEW_ESOP, PERM_VIEW_INVESTMENT,
    ];

    public function index()
    {
        $data['pageTitle']    = __('Share Classes');
        $data['shareClasses'] = ShareClass::with('permissions', 'shareholders')
            ->withCount('shareholders')
            ->orderBy('class_code')
            ->get();
        return view('admin.share-classes.index', $data);
    }

    public function show(ShareClass $shareClass)
    {
        $data['pageTitle']       = $shareClass->name;
        $data['shareClass']      = $shareClass->load('permissions', 'shareholders.user');
        $data['allPermissions']  = $this->allPermissions;
        return view('admin.share-classes.show', $data);
    }

    public function update(Request $request, ShareClass $shareClass)
    {
        $request->validate([
            'name'                                => 'required|max:191',
            'class_code'                          => 'required|max:5',
            'description'                         => 'nullable',
            'par_value'                           => 'nullable|numeric|min:0',
            'voting_multiplier'                   => 'required|numeric|min:0',
            'dividend_priority'                   => 'required|integer|min:1|max:100',
            'governance_level'                    => 'required|integer|in:1,2,3',
            'financial_approval_limit'            => 'nullable|numeric|min:0',
            'max_ownership_cap'                   => 'nullable|numeric|min:0|max:100',
            'fixed_dividend_rate'                 => 'nullable|numeric|min:0|max:100',
            'governance_notes'                    => 'nullable',
        ]);

        $shareClass->update([
            'name'                                => $request->name,
            'class_code'                          => strtoupper($request->class_code),
            'description'                         => $request->description,
            'par_value'                           => $request->par_value,
            'voting_rights'                       => $request->boolean('voting_rights'),
            'voting_multiplier'                   => $request->voting_multiplier,
            'dividend_rights'                     => $request->boolean('dividend_rights'),
            'dividend_priority'                   => $request->dividend_priority,
            'is_transferable'                     => $request->boolean('is_transferable'),
            'transfer_requires_board_approval'    => $request->boolean('transfer_requires_board_approval'),
            'transfer_requires_compliance_review' => $request->boolean('transfer_requires_compliance_review'),
            'can_be_diluted_without_approval'     => $request->boolean('can_be_diluted_without_approval'),
            'has_vesting'                         => $request->boolean('has_vesting'),
            'is_founder_class'                    => $request->boolean('is_founder_class'),
            'governance_level'                    => $request->governance_level,
            'financial_approval_limit'            => $request->financial_approval_limit,
            'max_ownership_cap'                   => $request->max_ownership_cap,
            'fixed_dividend_rate'                 => $request->fixed_dividend_rate,
            'dividend_cumulative'                 => $request->boolean('dividend_cumulative'),
            'governance_notes'                    => $request->governance_notes,
        ]);

        return $this->success([], __('Share class updated successfully.'));
    }

    public function updatePermissions(Request $request, ShareClass $shareClass)
    {
        $submitted = $request->input('permissions', []);

        foreach ($this->allPermissions as $key) {
            $isAllowed  = isset($submitted[$key]['is_allowed']) && $submitted[$key]['is_allowed'] == '1';
            $limitValue = $submitted[$key]['limit_value'] ?? null;
            if ($limitValue === '' || $limitValue === 'null') $limitValue = null;

            ShareClassPermission::updateOrCreate(
                ['share_class_id' => $shareClass->id, 'permission_key' => $key],
                ['is_allowed' => $isAllowed, 'limit_value' => $limitValue ? (float)$limitValue : null]
            );
        }

        return $this->success([], __('Permissions updated successfully.'));
    }
}
