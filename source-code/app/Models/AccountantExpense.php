<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountantExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number', 'owner_user_id', 'accountant_id', 'property_id',
        'property_unit_id', 'category', 'title', 'description', 'amount',
        'currency', 'payment_method', 'vendor_name', 'vendor_contact',
        'expense_date', 'status', 'approved_by', 'approved_at',
        'approval_note', 'receipt_file', 'ip_address',
    ];

    protected $casts = ['expense_date' => 'date', 'approved_at' => 'datetime'];

    public function accountant() { return $this->belongsTo(Accountant::class); }
    public function property()   { return $this->belongsTo(Property::class); }
    public function unit()       { return $this->belongsTo(PropertyUnit::class, 'property_unit_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getCategoryLabelAttribute(): string
    {
        return [
            1 => 'Maintenance', 2 => 'Utilities', 3 => 'Contractor',
            4 => 'Salary', 5 => 'Insurance', 6 => 'Legal',
            7 => 'Marketing', 8 => 'Other',
        ][$this->category] ?? 'Other';
    }

    public static function generateReference(int $ownerUserId): string
    {
        $count = self::where('owner_user_id', $ownerUserId)->withTrashed()->count() + 1;
        return 'EXP-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function scopePending($q)  { return $q->where('status', 0); }
    public function scopeApproved($q) { return $q->where('status', 1); }
    public function scopeThisMonth($q){ return $q->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year); }
}
