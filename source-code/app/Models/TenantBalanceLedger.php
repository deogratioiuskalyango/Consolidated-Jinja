<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantBalanceLedger extends Model
{
    public $timestamps = false;

    protected $table = 'tenant_balance_ledger';

    protected $fillable = [
        'owner_user_id', 'tenant_id', 'property_id', 'property_unit_id',
        'total_charged', 'total_paid', 'balance_due',
        'security_deposit_held', 'advance_credit', 'currency',
        'last_payment_at', 'updated_at', 'created_at',
    ];

    protected $casts = ['last_payment_at' => 'datetime'];

    public function tenant()   { return $this->belongsTo(Tenant::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function unit()     { return $this->belongsTo(PropertyUnit::class, 'property_unit_id'); }

    public static function credit(int $tenantId, int $unitId, int $ownerId, float $amount, string $type = 'rent'): void
    {
        $ledger = static::firstOrCreate(
            ['tenant_id' => $tenantId, 'property_unit_id' => $unitId],
            ['owner_user_id' => $ownerId, 'created_at' => now()]
        );
        $ledger->total_paid      += $amount;
        $ledger->balance_due      = max(0, $ledger->total_charged - $ledger->total_paid);
        $ledger->last_payment_at  = now();
        $ledger->updated_at       = now();
        $ledger->save();
    }

    public static function debit(int $tenantId, int $unitId, int $ownerId, float $amount): void
    {
        $ledger = static::firstOrCreate(
            ['tenant_id' => $tenantId, 'property_unit_id' => $unitId],
            ['owner_user_id' => $ownerId, 'created_at' => now()]
        );
        $ledger->total_charged += $amount;
        $ledger->balance_due    = max(0, $ledger->total_charged - $ledger->total_paid);
        $ledger->updated_at     = now();
        $ledger->save();
    }

    public function scopeOverdue($q) { return $q->where('balance_due', '>', 0); }
}
