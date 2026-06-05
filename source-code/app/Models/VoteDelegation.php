<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VoteDelegation extends Model
{
    protected $guarded = [];
    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function delegator()
    {
        return $this->belongsTo(Shareholder::class, 'delegator_shareholder_id');
    }

    public function delegatee()
    {
        return $this->belongsTo(Shareholder::class, 'delegatee_shareholder_id');
    }

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id');
    }

    public function isCurrentlyActive(): bool
    {
        $today = now()->toDateString();
        return $this->is_active
            && $this->valid_from->lte(now())
            && ($this->valid_until === null || $this->valid_until->gte(now()));
    }
}
