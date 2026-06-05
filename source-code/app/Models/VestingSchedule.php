<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VestingSchedule extends Model
{
    protected $guarded = [];
    protected $casts = [
        'vesting_start_date' => 'date',
        'cliff_date' => 'date',
        'vesting_end_date' => 'date',
        'forfeited_at' => 'datetime',
        'employment_linked' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }

    public function getSharesAvailableAttribute(): float
    {
        return max(0, $this->shares_vested - $this->shares_forfeited);
    }

    public function getVestingProgressPercentAttribute(): float
    {
        if ($this->total_shares_granted <= 0) return 0;
        return round(($this->shares_vested / $this->total_shares_granted) * 100, 2);
    }

    public function isCliffPassed(): bool
    {
        return !$this->cliff_date || now()->gte($this->cliff_date);
    }
}
