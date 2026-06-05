<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\FinancialApproval;
use App\Models\FinancialApprovalAction;
use App\Models\GovernanceAuditLog;
use App\Services\GovernanceEngine;
use Illuminate\Http\Request;

class FinancialApprovalController extends Controller
{
    public function __construct(private GovernanceEngine $engine) {}

    public function index()
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        $ownerUserId = $shareholder->owner_user_id;

        $data['pageTitle']   = __('Financial Approvals');
        $data['shareholder'] = $shareholder;
        $data['rights']      = $this->engine->getRightsSummary($shareholder);
        $data['canApprove']  = $this->engine->hasPermission($shareholder, PERM_APPROVE_EXPENSES);

        $data['approvals'] = FinancialApproval::where('owner_user_id', $ownerUserId)
            ->with(['actions' => fn($q) => $q->where('shareholder_id', $shareholder->id)])
            ->orderByRaw('status = ? DESC', [APPROVAL_STATUS_PENDING])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('shareholder.financial-approvals.index', $data);
    }

    public function show(FinancialApproval $approval)
    {
        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        abort_if($approval->owner_user_id !== $shareholder->owner_user_id, 403);

        $data['pageTitle']    = $approval->title;
        $data['approval']     = $approval->load('actions.shareholder.user');
        $data['myAction']     = $approval->actions->where('shareholder_id', $shareholder->id)->first();
        $data['shareholder']  = $shareholder;
        $data['canApprove']   = $this->engine->canApproveExpense($shareholder, (float) $approval->amount);
        $data['approvalLimit']= $this->engine->getPermissionLimit($shareholder, PERM_APPROVE_EXPENSES);
        $data['threshold']    = $this->engine->getApplicableThreshold($shareholder->owner_user_id, (float) $approval->amount);

        return view('shareholder.financial-approvals.show', $data);
    }

    public function action(Request $request, FinancialApproval $approval)
    {
        $request->validate([
            'action'  => 'required|in:1,2',
            'comment' => 'nullable|max:1000',
        ]);

        $shareholder = auth()->user()->shareholder->loadMissing('shareClass.permissions');
        abort_if($approval->owner_user_id !== $shareholder->owner_user_id, 403);
        abort_if($approval->status !== APPROVAL_STATUS_PENDING, 400, 'This approval is no longer pending.');
        abort_if($approval->actions()->where('shareholder_id', $shareholder->id)->exists(), 400, 'You have already acted on this request.');

        // GovernanceEngine check — only approvals (not rejections) need the permission
        if ($request->action == APPROVAL_ACTION_APPROVE) {
            if (!$this->engine->canApproveExpense($shareholder, (float) $approval->amount)) {
                return redirect()->back()->with('error',
                    __('Your share class does not have authority to approve an expense of this amount.')
                );
            }
        }

        FinancialApprovalAction::create([
            'financial_approval_id' => $approval->id,
            'shareholder_id'        => $shareholder->id,
            'action'                => $request->action,
            'comment'               => $request->comment,
            'voting_weight'         => $shareholder->ownership_percentage,
            'ip_address'            => $request->ip(),
            'device_info'           => substr($request->userAgent(), 0, 191),
            'acted_at'              => now(),
        ]);

        $approval->computeStatus($shareholder->owner_user_id);

        $auditAction = $request->action == APPROVAL_ACTION_APPROVE ? AUDIT_APPROVE : AUDIT_REJECT;
        GovernanceAuditLog::record(
            $auditAction,
            $approval,
            ['action' => $request->action, 'class' => $shareholder->class_code, 'amount' => $approval->amount],
            'Financial approval action on: ' . $approval->title
        );

        return redirect()->back()->with('success', $request->action == APPROVAL_ACTION_APPROVE
            ? __('Approval recorded.')
            : __('Rejection recorded.'));
    }
}
