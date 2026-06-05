<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Traits\ResponseTrait;

class AuditLogController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $query = FinancialAuditLog::where('owner_user_id', $ownerUserId)
            ->orderByDesc('created_at');

        // Optional filter by action
        if ($action = request('action')) {
            $query->where('action', $action);
        }

        $logs = $query->paginate(20);

        // Build distinct action list for filter dropdown
        $actions = [
            AUDIT_PAYMENT_RECORDED => 'Payment Recorded',
            AUDIT_PAYMENT_REVERSED => 'Payment Reversed',
            AUDIT_EXPENSE_CREATED  => 'Expense Created',
            AUDIT_EXPENSE_APPROVED => 'Expense Approved',
            AUDIT_EXPENSE_REJECTED => 'Expense Rejected',
            AUDIT_REPORT_GENERATED => 'Report Generated',
            AUDIT_REPORT_VIEWED    => 'Report Viewed',
            AUDIT_RECONCILIATION   => 'Reconciliation',
            AUDIT_BALANCE_ADJUSTED => 'Balance Adjusted',
        ];

        return view('accountant.audit-logs', [
            'logs'      => $logs,
            'actions'   => $actions,
            'pageTitle' => 'Audit Logs',
        ]);
    }
}
