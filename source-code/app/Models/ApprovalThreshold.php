<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalThreshold extends Model
{
    protected $guarded = [];
    protected $casts = [
        'required_approver_classes' => 'array',
        'requires_quorum' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Does a given shareholder's share class qualify for this threshold?
    public function qualifies(Shareholder $shareholder): bool
    {
        $code = $shareholder->shareClass?->class_code;
        return $code && in_array($code, $this->required_approver_classes ?? []);
    }
}
