<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ContractClauseReview;
use App\Models\TenancyContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * List all contracts for the authenticated tenant.
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant->id;

        $data['pageTitle']  = __('My Contracts');
        $data['contracts']  = TenancyContract::where('tenant_id', $tenantId)
            ->latest()
            ->paginate(10);

        return view('tenant.contracts.index', $data);
    }

    /**
     * Show a single contract with its clause reviews.
     */
    public function show($id)
    {
        $contract = TenancyContract::with('clauses')->findOrFail($id);

        if ($contract->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }

        $data['pageTitle'] = __('Contract Details');
        $data['contract']  = $contract;

        // Key clauses by clause_key for easy lookup in view
        $data['clauseMap'] = $contract->clauses->keyBy('clause_key');

        return view('tenant.contracts.show', $data);
    }

    /**
     * AJAX — tenant reviews a single clause (agree / dispute).
     */
    public function reviewClause(Request $request, $id)
    {
        $contract = TenancyContract::with('clauses')->findOrFail($id);

        if ($contract->tenant_id !== auth()->user()->tenant->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'clause_key'     => 'required|string',
            'status'         => 'required|in:agreed,disputed',
            'tenant_comment' => 'nullable|string|max:1000',
        ]);

        $clause = ContractClauseReview::where('contract_id', $id)
            ->where('clause_key', $request->clause_key)
            ->firstOrFail();

        $clause->update([
            'status'         => $request->status,
            'tenant_comment' => $request->tenant_comment,
            'reviewed_at'    => now(),
        ]);

        // Reload clauses fresh
        $contract->load('clauses');
        $clauses = $contract->clauses;

        if ($clauses->contains('status', ContractClauseReview::STATUS_DISPUTED)) {
            $newStatus = TenancyContract::STATUS_DISPUTED;
        } elseif ($clauses->every(fn($c) => $c->status !== ContractClauseReview::STATUS_PENDING)) {
            $newStatus = TenancyContract::STATUS_PENDING_SIGNATURE;
        } else {
            $newStatus = TenancyContract::STATUS_REVIEWING;
        }

        $contract->update(['status' => $newStatus]);

        return response()->json([
            'status'        => 200,
            'message'       => __('Clause review saved successfully.'),
            'clause_status' => $clause->status,
            'contract_status' => $newStatus,
        ]);
    }

    /**
     * AJAX — tenant signs the contract.
     */
    public function sign(Request $request, $id)
    {
        $contract = TenancyContract::findOrFail($id);

        if ($contract->tenant_id !== auth()->user()->tenant->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'tenant_signature' => 'required|string',
        ]);

        $contract->tenant_signature = $request->tenant_signature;
        $contract->tenant_signed_at = now();

        if ($contract->landlord_signature && $contract->lc1_signature) {
            $contract->status = TenancyContract::STATUS_ACTIVE;
        } elseif ($contract->status !== TenancyContract::STATUS_PENDING_SIGNATURE) {
            $contract->status = TenancyContract::STATUS_PENDING_SIGNATURE;
        }

        $contract->save();

        return response()->json([
            'status'  => 200,
            'message' => __('Signed successfully.'),
        ]);
    }

    /**
     * AJAX — LC1 Chairperson signs the contract.
     */
    public function lc1Sign(Request $request, $id)
    {
        $contract = TenancyContract::findOrFail($id);

        if ($contract->tenant_id !== auth()->user()->tenant->id) {
            return response()->json(['status' => 403, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'lc1_signature' => 'required|string',
            'lc1_name'      => 'sometimes|nullable|string|max:255',
            'lc1_phone'     => 'sometimes|nullable|string|max:50',
            'lc1_stamp'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $contract->lc1_signature = $request->lc1_signature;
        $contract->lc1_signed_at = now();

        if ($request->filled('lc1_name')) {
            $contract->lc1_name = $request->lc1_name;
        }
        if ($request->filled('lc1_phone')) {
            $contract->lc1_phone = $request->lc1_phone;
        }

        if ($request->hasFile('lc1_stamp')) {
            $path = $request->file('lc1_stamp')->store('contracts/stamps', 'public');
            $contract->lc1_stamp = $path;
        }

        if ($contract->tenant_signature && $contract->landlord_signature) {
            $contract->status = TenancyContract::STATUS_ACTIVE;
        } elseif ($contract->status !== TenancyContract::STATUS_PENDING_SIGNATURE) {
            $contract->status = TenancyContract::STATUS_PENDING_SIGNATURE;
        }

        $contract->save();

        return response()->json([
            'status'  => 200,
            'message' => __('LC1 signature saved successfully.'),
        ]);
    }

    /**
     * Print-ready contract view (standalone HTML, no layout).
     */
    public function print($id)
    {
        $contract = TenancyContract::with(['clauses', 'property', 'unit', 'tenant'])
            ->findOrFail($id);

        if ($contract->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }

        $clauseMap = $contract->clauses->keyBy('clause_key');

        return view('tenant.contracts.print', compact('contract', 'clauseMap'));
    }
}
