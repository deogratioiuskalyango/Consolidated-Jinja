<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DividendPayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function declaration()
    {
        return $this->belongsTo(DividendDeclaration::class, 'declaration_id');
    }

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            DIVIDEND_STATUS_DECLARED  => 'Declared',
            DIVIDEND_STATUS_PAID      => 'Paid',
            DIVIDEND_STATUS_CANCELLED => 'Cancelled',
            default                   => 'Unknown',
        };
    }
}
