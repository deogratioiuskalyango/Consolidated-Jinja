<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DividendDeclaration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'declaration_date' => 'date',
        'payment_date'     => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(DividendPayment::class, 'declaration_id');
    }

    public function declaredBy()
    {
        return $this->belongsTo(User::class, 'declared_by');
    }

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id');
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

    public function generatePayments(): void
    {
        $shareholders = Shareholder::where('owner_user_id', $this->owner_user_id)
            ->where('status', SHAREHOLDER_STATUS_ACTIVE)
            ->get();

        foreach ($shareholders as $shareholder) {
            $amount = ($shareholder->shares_held ?? 0) * ($this->dividend_per_share ?? 0);

            DividendPayment::firstOrCreate(
                [
                    'declaration_id' => $this->id,
                    'shareholder_id' => $shareholder->id,
                ],
                [
                    'amount'          => $amount,
                    'status'          => DIVIDEND_STATUS_DECLARED,
                    'currency'        => $this->currency ?? 'USD',
                ]
            );
        }
    }
}
