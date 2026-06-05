<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractClauseReview;
use App\Models\Tenant;
use App\Models\TenancyContract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * List all contracts, paginated.
     */
    public function index()
    {
        $contracts = TenancyContract::with(['tenant.user', 'property'])
            ->latest()
            ->paginate(15);

        $pageTitle = 'Contracts';

        return view('admin.contracts.index', compact('contracts', 'pageTitle'));
    }

    /**
     * Show the create contract form.
     */
    public function create()
    {
        $tenants = Tenant::with(['user', 'property', 'unit'])->get();
        $pageTitle = 'Create Contract';

        return view('admin.contracts.create', compact('tenants', 'pageTitle'));
    }

    /**
     * Store a newly created contract.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'         => 'required|exists:tenants,id',
            'commencement_date' => 'required|date',
            'expiry_date'       => 'required|date|after:commencement_date',
            'monthly_rent'      => 'required|numeric|min:0',
            'security_deposit'  => 'required|numeric|min:0',
            'payment_due_day'   => 'sometimes|integer|between:1,28',
            'special_conditions'=> 'sometimes|string|nullable',
        ]);

        // Load tenant with its relations to auto-populate fields
        $tenant = Tenant::with(['user', 'property', 'unit'])->findOrFail($request->tenant_id);

        $contract = TenancyContract::create([
            'owner_user_id'      => auth()->id(),
            'tenant_id'          => $tenant->id,
            'property_id'        => $tenant->property_id ?? null,
            'unit_id'            => $tenant->unit_id ?? null,
            'status'             => TenancyContract::STATUS_DRAFT,

            // Auto-populate from tenant relations
            'tenant_name'        => $tenant->user?->first_name . ' ' . $tenant->user?->last_name,
            'tenant_email'       => $tenant->user?->email,
            'tenant_phone'       => $tenant->user?->contact_number,
            'property_name'      => $tenant->property?->name ?? null,
            'unit_name'          => $tenant->unit?->unit_name ?? null,

            // Section C - Payment
            'monthly_rent'       => $request->monthly_rent,
            'security_deposit'   => $request->security_deposit,
            'currency'           => $request->input('currency', 'UGX'),
            'payment_due_day'    => $request->input('payment_due_day', 5),
            'payment_method'     => $request->input('payment_method'),

            // Section D - Bank Details
            'bank_name'          => $request->input('bank_name'),
            'bank_account_name'  => $request->input('bank_account_name'),
            'bank_account_number'=> $request->input('bank_account_number'),
            'bank_branch'        => $request->input('bank_branch'),
            'mobile_money_number'=> $request->input('mobile_money_number'),

            // Section E - Period
            'commencement_date'  => $request->commencement_date,
            'expiry_date'        => $request->expiry_date,
            'notice_period_days' => $request->input('notice_period_days', 30),

            // LC1
            'lc1_name'           => $request->input('lc1_name'),
            'lc1_phone'          => $request->input('lc1_phone'),

            // Witnesses
            'witness1_name'      => $request->input('witness1_name'),
            'witness2_name'      => $request->input('witness2_name'),

            // Special conditions
            'special_conditions' => $request->input('special_conditions'),
        ]);

        // Create all 10 clause reviews with status 'pending'
        foreach (array_keys(ContractClauseReview::CLAUSES) as $clauseKey) {
            ContractClauseReview::create([
                'contract_id' => $contract->id,
                'clause_key'  => $clauseKey,
                'status'      => ContractClauseReview::STATUS_PENDING,
            ]);
        }

        return redirect()
            ->route('admin.contracts.index')
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Show contract details.
     */
    public function show($id)
    {
        $contract = TenancyContract::with([
            'tenant.user',
            'property',
            'unit',
            'clauses',
        ])->findOrFail($id);

        $pageTitle = 'Contract Details';

        return view('admin.contracts.show', compact('contract', 'pageTitle'));
    }

    /**
     * Change contract status from draft to sent.
     */
    public function send($id)
    {
        $contract = TenancyContract::findOrFail($id);

        if ($contract->status === TenancyContract::STATUS_DRAFT) {
            $contract->status = TenancyContract::STATUS_SENT;
            $contract->save();
        }

        return redirect()->back()->with('success', 'Contract sent to tenant successfully.');
    }

    /**
     * Save the landlord/admin signature (base64) and update status.
     */
    public function adminSign(Request $request, $id)
    {
        $request->validate([
            'landlord_signature' => 'required|string',
        ]);

        $contract = TenancyContract::findOrFail($id);

        $contract->landlord_signature = $request->landlord_signature;
        $contract->landlord_signed_at = now();

        $this->recalculateStatus($contract);
        $contract->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Signed successfully.',
        ]);
    }

    /**
     * Save the LC1 Chairperson signature, stamp, and optional name/phone.
     */
    public function lc1Sign(Request $request, $id)
    {
        $request->validate([
            'lc1_signature' => 'required|string',
            'lc1_name'      => 'sometimes|nullable|string|max:255',
            'lc1_phone'     => 'sometimes|nullable|string|max:50',
            'lc1_stamp'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $contract = TenancyContract::findOrFail($id);

        $contract->lc1_signature = $request->lc1_signature;
        $contract->lc1_signed_at = now();

        if ($request->filled('lc1_name'))  $contract->lc1_name  = $request->lc1_name;
        if ($request->filled('lc1_phone')) $contract->lc1_phone = $request->lc1_phone;

        if ($request->hasFile('lc1_stamp')) {
            $contract->lc1_stamp = $request->file('lc1_stamp')->store('contracts/stamps', 'public');
        }

        $this->recalculateStatus($contract);
        $contract->save();

        return response()->json(['status' => 200, 'message' => __('LC1 signature saved successfully.')]);
    }

    /**
     * Save Witness 1 signature (and optional name if not already set).
     */
    public function witness1Sign(Request $request, $id)
    {
        $request->validate([
            'witness1_signature' => 'required|string',
            'witness1_name'      => 'sometimes|nullable|string|max:255',
        ]);

        $contract = TenancyContract::findOrFail($id);

        $contract->witness1_signature = $request->witness1_signature;
        $contract->witness1_signed_at = now();

        if ($request->filled('witness1_name')) $contract->witness1_name = $request->witness1_name;

        $contract->save();

        return response()->json(['status' => 200, 'message' => __('Witness 1 signature saved successfully.')]);
    }

    /**
     * Check all three required signatures and promote status to active if complete.
     */
    private function recalculateStatus(TenancyContract $contract): void
    {
        if ($contract->tenant_signature && $contract->landlord_signature && $contract->lc1_signature) {
            $contract->status = TenancyContract::STATUS_ACTIVE;
        } elseif ($contract->status !== TenancyContract::STATUS_PENDING_SIGNATURE) {
            $contract->status = TenancyContract::STATUS_PENDING_SIGNATURE;
        }
    }

    /**
     * Soft-delete the contract.
     */
    public function destroy($id)
    {
        $contract = TenancyContract::findOrFail($id);
        $contract->delete();

        return redirect()
            ->route('admin.contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Return tenant info as JSON for AJAX calls.
     */
    public function tenantInfo(Request $request)
    {
        $tenant = Tenant::with(['user', 'property', 'unit'])
            ->findOrFail($request->tenant_id);

        return response()->json([
            'status' => 200,
            'data'   => [
                'name'          => $tenant->user?->first_name . ' ' . $tenant->user?->last_name,
                'email'         => $tenant->user?->email,
                'phone'         => $tenant->user?->contact_number,
                'property_name' => $tenant->property?->name,
                'unit_name'     => $tenant->unit?->unit_name,
                'property_id'   => $tenant->property_id,
                'unit_id'       => $tenant->unit_id,
            ],
        ]);
    }
}
