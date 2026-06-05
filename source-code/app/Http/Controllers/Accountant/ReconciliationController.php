<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationLog;
use App\Models\FinancialAuditLog;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    use ResponseTrait;

    /**
     * Reconciliation type labels keyed by constant value.
     */
    private function reconTypes(): array
    {
        return [
            RECON_TYPE_MTN_MOMO     => 'MTN Mobile Money',
            RECON_TYPE_AIRTEL_MONEY => 'Airtel Money',
            RECON_TYPE_BANK         => 'Bank Transfer',
            RECON_TYPE_CASH         => 'Cash',
        ];
    }

    public function index()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $logs = ReconciliationLog::where('owner_user_id', $ownerUserId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('accountant.reconciliation.index', [
            'logs'       => $logs,
            'reconTypes' => $this->reconTypes(),
            'pageTitle'  => 'Reconciliation',
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'recon_type'       => 'required|integer|between:1,4',
            'transaction_ref'  => 'required|string|max:191',
            'amount'           => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description'      => 'nullable|string',
        ]);

        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        DB::beginTransaction();
        try {
            $log = ReconciliationLog::create([
                'owner_user_id'    => $ownerUserId,
                'accountant_id'    => $accountant->id,
                'recon_type'       => $request->recon_type,
                'transaction_ref'  => $request->transaction_ref,
                'amount'           => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description'      => $request->description,
                'status'           => RECON_STATUS_UNMATCHED,
            ]);

            FinancialAuditLog::record(
                AUDIT_RECONCILIATION,
                $accountant,
                [
                    'before' => [],
                    'after'  => [
                        'log_id'          => $log->id,
                        'transaction_ref' => $request->transaction_ref,
                        'amount'          => $request->amount,
                        'recon_type'      => $request->recon_type,
                    ],
                ],
                'Transaction uploaded for reconciliation — Ref: ' . $request->transaction_ref
            );

            DB::commit();
            return $this->success([], 'Transaction uploaded for reconciliation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function match(ReconciliationLog $log, Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:rent_collections,id',
        ]);

        DB::beginTransaction();
        try {
            $log->update([
                'status'               => RECON_STATUS_MATCHED,
                'matched_collection_id'=> $request->collection_id,
                'matched_at'           => now(),
            ]);

            DB::commit();
            return $this->success([], 'Transaction matched.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function flag(ReconciliationLog $log, Request $request)
    {
        $request->validate([
            'status'      => 'required|integer|in:3,4',
            'flag_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $log->update([
                'status'      => $request->status,
                'flag_reason' => $request->flag_reason,
            ]);

            DB::commit();
            return $this->success([], 'Transaction flagged.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
