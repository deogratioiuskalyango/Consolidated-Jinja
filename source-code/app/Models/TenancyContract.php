<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenancyContract extends Model
{
    use SoftDeletes;

    // ─── Status constants ────────────────────────────────────────────────────
    const STATUS_DRAFT             = 'draft';
    const STATUS_SENT              = 'sent';
    const STATUS_REVIEWING         = 'reviewing';
    const STATUS_DISPUTED          = 'disputed';
    const STATUS_PENDING_SIGNATURE = 'pending_signature';
    const STATUS_SIGNED            = 'signed';
    const STATUS_ACTIVE            = 'active';
    const STATUS_TERMINATED        = 'terminated';

    // ─── Mass-assignable columns ─────────────────────────────────────────────
    protected $fillable = [
        'owner_user_id',
        'tenant_id',
        'property_id',
        'unit_id',
        'reference_no',
        'status',

        // Section A
        'tenant_name',
        'tenant_phone',
        'tenant_email',
        'tenant_nationality',
        'tenant_id_type',
        'tenant_id_number',
        'next_of_kin_name',
        'next_of_kin_phone',

        // Section B
        'property_name',
        'property_address',
        'unit_name',

        // Section C
        'monthly_rent',
        'security_deposit',
        'currency',
        'payment_due_day',
        'payment_method',

        // Section D
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_branch',
        'mobile_money_number',

        // Section E
        'commencement_date',
        'expiry_date',
        'notice_period_days',

        // Contract text
        'special_conditions',

        // Signatures
        'tenant_signature',
        'tenant_signed_at',
        'landlord_signature',
        'landlord_signed_at',
        'lc1_name',
        'lc1_phone',
        'lc1_signature',
        'lc1_stamp',
        'lc1_signed_at',
        'witness1_name',
        'witness1_signature',
        'witness1_signed_at',
        'witness2_name',
        'witness2_signature',
        'witness2_signed_at',
    ];

    protected $casts = [
        'commencement_date'   => 'date',
        'expiry_date'         => 'date',
        'tenant_signed_at'    => 'datetime',
        'landlord_signed_at'  => 'datetime',
        'lc1_signed_at'       => 'datetime',
        'witness1_signed_at'  => 'datetime',
        'witness2_signed_at'  => 'datetime',
        'monthly_rent'        => 'decimal:2',
        'security_deposit'    => 'decimal:2',
    ];

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TenancyContract $contract) {
            if (empty($contract->reference_no)) {
                $contract->reference_no = 'TC-' . strtoupper(uniqid());
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }

    public function property(): HasOne
    {
        return $this->hasOne(Property::class, 'id', 'property_id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(PropertyUnit::class, 'id', 'unit_id');
    }

    public function clauses(): HasMany
    {
        return $this->hasMany(ContractClauseReview::class, 'contract_id');
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_user_id');
    }
}
