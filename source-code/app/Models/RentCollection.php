<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentCollection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receipt_number', 'owner_user_id', 'accountant_id', 'tenant_id',
        'property_id', 'property_unit_id', 'invoice_id', 'collection_type',
        'payment_method', 'amount', 'balance_before', 'balance_after',
        'currency', 'transaction_ref', 'collected_by', 'status',
        'payment_date', 'notes', 'reversed_by', 'reversed_at',
        'reversal_reason', 'ip_address',
    ];

    protected $casts = ['payment_date' => 'date', 'reversed_at' => 'datetime'];

    public function tenant()       { return $this->belongsTo(Tenant::class); }
    public function property()     { return $this->belongsTo(Property::class); }
    public function unit()         { return $this->belongsTo(PropertyUnit::class, 'property_unit_id'); }
    public function accountant()   { return $this->belongsTo(Accountant::class); }

    public function getPaymentMethodLabelAttribute(): string
    {
        return [
            1 => 'Cash', 2 => 'Bank Transfer', 3 => 'MTN Mobile Money',
            4 => 'Airtel Money', 5 => 'Cheque', 6 => 'Card',
        ][$this->payment_method] ?? 'Unknown';
    }

    public function getCollectionTypeLabelAttribute(): string
    {
        return [
            1 => 'Rent', 2 => 'Security Deposit', 3 => 'Utility',
            4 => 'Penalty', 5 => 'Advance Rent', 6 => 'Partial Payment',
        ][$this->collection_type] ?? 'Other';
    }

    public static function generateReceiptNumber(int $ownerUserId): string
    {
        $count = self::where('owner_user_id', $ownerUserId)->withTrashed()->count() + 1;
        return 'RCT-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function scopeConfirmed($q)   { return $q->where('status', 1); }
    public function scopeToday($q)       { return $q->whereDate('payment_date', today()); }
    public function scopeThisWeek($q)    { return $q->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]); }
    public function scopeThisMonth($q)   { return $q->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year); }
}
