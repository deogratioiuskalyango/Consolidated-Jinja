<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialApprovalAction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function financialApproval()
    {
        return $this->belongsTo(FinancialApproval::class, 'financial_approval_id');
    }

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }
}
