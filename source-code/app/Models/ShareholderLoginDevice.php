<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareholderLoginDevice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }
}
