<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(GovernanceMeeting::class, 'meeting_id');
    }

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }
}
