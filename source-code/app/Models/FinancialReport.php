<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number', 'owner_user_id', 'generated_by', 'report_type',
        'title', 'period_from', 'period_to', 'report_data', 'status',
        'shared_with_shareholders', 'shared_at', 'property_id',
    ];

    protected $casts = [
        'period_from'               => 'date',
        'period_to'                 => 'date',
        'report_data'               => 'array',
        'shared_with_shareholders'  => 'boolean',
        'shared_at'                 => 'datetime',
    ];

    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }
    public function property()    { return $this->belongsTo(Property::class); }

    public function getReportTypeLabelAttribute(): string
    {
        return [1 => 'Weekly', 2 => 'Monthly', 3 => 'Quarterly', 4 => 'Annual', 5 => 'Custom'][$this->report_type] ?? 'Custom';
    }

    public static function generateReference(): string
    {
        return 'RPT-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(4));
    }
}
