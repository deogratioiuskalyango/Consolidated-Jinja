<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\RentCollection;
use App\Models\TenantBalanceLedger;
use App\Models\FinancialAuditLog;
use App\Models\Tenant;
use App\Models\Property;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RentCollectionController extends Controller
{
    use ResponseTrait;

    /**
     * Payment method labels keyed by constant value.
     */
    private function paymentMethods(): array
    {
        return [
            PAYMENT_METHOD_CASH         => 'Cash',
            PAYMENT_METHOD_BANK         => 'Bank Transfer',
            PAYMENT_METHOD_MTN_MOMO     => 'MTN Mobile Money',
            PAYMENT_METHOD_AIRTEL_MONEY => 'Airtel Money',
            PAYMENT_METHOD_CHEQUE       => 'Cheque',
            PAYMENT_METHOD_CARD         => 'Card',
        ];
    }

    /**
     * Collection type labels keyed by constant value.
     */
    private function collectionTypes(): array
    {
        return [
            COLLECTION_TYPE_RENT             => 'Rent',
            COLLECTION_TYPE_SECURITY_DEPOSIT => 'Security Deposit',
            COLLECTION_TYPE_UTILITY          => 'Utility',
            COLLECTION_TYPE_PENALTY          => 'Penalty',
            COLLECTION_TYPE_ADVANCE          => 'Advance',
            COLLECTION_TYPE_PARTIAL          => 'Partial Payment',
        ];
    }

    public function index()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $collections = RentCollection::where('owner_user_id', $ownerUserId)
            ->with(['tenant', 'property'])
            ->orderByDesc('payment_date')
            ->paginate(20);

        return view('accountant.collections.index', [
            'collections'     => $collections,
            'paymentMethods'  => $this->paymentMethods(),
            'collectionTypes' => $this->collectionTypes(),
            'pageTitle'       => 'Rent Collections',
        ]);
    }

    public function create()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $tenants    = Tenant::where('owner_user_id', $ownerUserId)->with('user')->get();
        $properties = Property::where('owner_user_id', $ownerUserId)->get();

        return view('accountant.collections.create', [
            'tenants'         => $tenants,
            'properties'      => $properties,
            'paymentMethods'  => $this->paymentMethods(),
            'collectionTypes' => $this->collectionTypes(),
            'pageTitle'       => 'Record Payment',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'       => 'required|exists:tenants,id',
            'property_id'     => 'required|exists:properties,id',
            'collection_type' => 'required|integer|between:1,6',
            'payment_method'  => 'required|integer|between:1,6',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'transaction_ref' => 'nullable|string|max:191',
            'notes'           => 'nullable|string',
        ]);

        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        DB::beginTransaction();
        try {
            // Balance before credit
            $ledger        = TenantBalanceLedger::where('owner_user_id', $ownerUserId)
                ->where('tenant_id', $request->tenant_id)
                ->first();
            $balanceBefore = $ledger ? $ledger->balance_due : 0;

            // Generate unique receipt number
            $receiptNumber = RentCollection::generateReceiptNumber($ownerUserId);

            $collection = RentCollection::create([
                'owner_user_id'   => $ownerUserId,
                'accountant_id'   => $accountant->id,
                'tenant_id'       => $request->tenant_id,
                'property_id'     => $request->property_id,
                'collection_type' => $request->collection_type,
                'payment_method'  => $request->payment_method,
                'amount'          => $request->amount,
                'payment_date'    => $request->payment_date,
                'transaction_ref' => $request->transaction_ref,
                'notes'           => $request->notes,
                'status'          => COLLECTION_STATUS_CONFIRMED,
                'receipt_number'  => $receiptNumber,
                'ip_address'      => request()->ip(),
            ]);

            // Credit tenant balance ledger
            TenantBalanceLedger::credit(
                $ownerUserId,
                $request->tenant_id,
                $request->amount,
                'Payment recorded — Receipt: ' . $receiptNumber
            );

            $balanceAfter = $balanceBefore - $request->amount;

            FinancialAuditLog::record(
                AUDIT_PAYMENT_RECORDED,
                $accountant,
                [
                    'before' => ['balance_due' => $balanceBefore],
                    'after'  => ['balance_due' => $balanceAfter, 'receipt_number' => $receiptNumber],
                ],
                'Payment recorded for tenant #' . $request->tenant_id . ' — ' . $receiptNumber
            );

            DB::commit();
            return $this->success([], 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function show(RentCollection $collection)
    {
        $collection->load(['tenant', 'property', 'unit', 'accountant']);

        return view('accountant.collections.show', [
            'collection' => $collection,
            'pageTitle'  => 'Collection Details',
        ]);
    }

    public function receipt(RentCollection $collection)
    {
        $collection->load(['tenant', 'property', 'unit', 'accountant']);

        return view('accountant.collections.receipt', [
            'collection' => $collection,
            'pageTitle'  => 'Payment Receipt',
        ]);
    }

    public function reverse(RentCollection $collection, Request $request)
    {
        if ($collection->status === COLLECTION_STATUS_REVERSED) {
            return $this->error([], 'This payment has already been reversed.');
        }

        $request->validate([
            'reversal_reason' => 'required|string|max:500',
        ]);

        $accountant  = auth()->user()->accountant;
        $ownerUserId = $accountant->owner_user_id;

        DB::beginTransaction();
        try {
            $amountBefore = $collection->amount;

            $collection->update([
                'status'          => COLLECTION_STATUS_REVERSED,
                'reversed_by'     => auth()->id(),
                'reversed_at'     => now(),
                'reversal_reason' => $request->reversal_reason,
            ]);

            // Reverse the credit by debiting the ledger
            TenantBalanceLedger::debit(
                $ownerUserId,
                $collection->tenant_id,
                $amountBefore,
                'Payment reversal — Receipt: ' . $collection->receipt_number
            );

            FinancialAuditLog::record(
                AUDIT_PAYMENT_REVERSED,
                $accountant,
                [
                    'before' => ['status' => COLLECTION_STATUS_CONFIRMED],
                    'after'  => ['status' => COLLECTION_STATUS_REVERSED, 'reversal_reason' => $request->reversal_reason],
                ],
                'Payment reversed — Receipt: ' . $collection->receipt_number
            );

            DB::commit();
            return $this->success([], 'Payment reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function balances()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $balances = TenantBalanceLedger::where('owner_user_id', $ownerUserId)
            ->with('tenant')
            ->get();

        return view('accountant.balances', [
            'balances'  => $balances,
            'pageTitle' => 'Tenant Balances',
        ]);
    }

    public function getData()
    {
        $ownerUserId = auth()->user()->accountant->owner_user_id;

        $collections = RentCollection::where('owner_user_id', $ownerUserId)
            ->with(['tenant', 'property'])
            ->orderByDesc('payment_date');

        return DataTables::of($collections)->make(true);
    }
}
