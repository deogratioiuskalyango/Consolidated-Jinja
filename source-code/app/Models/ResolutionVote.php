<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResolutionVote extends Model
{
    protected $guarded = [];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id');
    }

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }
}
