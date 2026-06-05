<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationLog extends Model
{
    protected $fillable = [
        'owner_user_id', 'accountant_id', 'recon_type', 'external_ref',
        'payer_name', 'payer_phone', 'amount', 'currency',
        'transaction_date', 'status', 'matched_collection_id',
        'notes', 'flag_reason', 'ip_address',
    ];

    protected $casts = ['transaction_date' => 'date'];

    public function matchedCollection() { return $this->belongsTo(RentCollection::class, 'matched_collection_id'); }
    public function accountant()        { return $this->belongsTo(Accountant::class); }

    public function getReconTypeLabelAttribute(): string
    {
        return [1 => 'MTN MoMo', 2 => 'Airtel Money', 3 => 'Bank', 4 => 'Cash'][$this->recon_type] ?? 'Unknown';
    }

    public function getStatusLabelAttribute(): string
    {
        return [1 => 'Matched', 2 => 'Unmatched', 3 => 'Duplicate', 4 => 'Suspicious'][$this->status] ?? 'Unknown';
    }
}
