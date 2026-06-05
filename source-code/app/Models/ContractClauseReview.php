<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractClauseReview extends Model
{
    // ─── Status constants ────────────────────────────────────────────────────
    const STATUS_PENDING  = 'pending';
    const STATUS_AGREED   = 'agreed';
    const STATUS_DISPUTED = 'disputed';

    // ─── Standard clause keys → human-readable labels ────────────────────────
    const CLAUSES = [
        'term_of_tenancy'    => 'Term of Tenancy',
        'rent_payment'       => 'Rent & Payment',
        'security_deposit'   => 'Security Deposit',
        'landlord_obligations' => 'Landlord Obligations',
        'tenant_obligations' => 'Tenant Obligations',
        'utilities'          => 'Utilities & Services',
        'termination'        => 'Termination',
        'lock_in_period'     => 'Lock-in Period',
        'alterations'        => 'Alterations & Improvements',
        'dispute_resolution' => 'Dispute Resolution',
    ];

    // ─── Mass-assignable columns ─────────────────────────────────────────────
    protected $fillable = [
        'contract_id',
        'clause_key',
        'status',
        'tenant_comment',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(TenancyContract::class, 'contract_id');
    }
}
