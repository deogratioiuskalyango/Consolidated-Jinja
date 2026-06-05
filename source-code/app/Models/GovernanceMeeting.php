<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GovernanceMeeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'started_at'         => 'datetime',
        'ended_at'           => 'datetime',
        'recurrence_end_date'=> 'date',
        'is_recurring'       => 'boolean',
    ];

    public function attendees()
    {
        return $this->hasMany(MeetingAttendee::class, 'meeting_id');
    }

    public function shareholders()
    {
        return $this->belongsToMany(Shareholder::class, 'meeting_attendees', 'meeting_id', 'shareholder_id');
    }

    public function resolutions()
    {
        return $this->hasMany(Resolution::class, 'meeting_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parentMeeting()
    {
        return $this->belongsTo(GovernanceMeeting::class, 'parent_meeting_id');
    }

    public function childMeetings()
    {
        return $this->hasMany(GovernanceMeeting::class, 'parent_meeting_id');
    }

    public function documents()
    {
        return $this->hasMany(GovernanceDocument::class, 'meeting_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ((int) $this->type) {
            MEETING_TYPE_AGM   => 'AGM',
            MEETING_TYPE_BOARD => 'Board Meeting',
            MEETING_TYPE_EGM   => 'EGM',
            MEETING_TYPE_OTHER => 'Other',
            default            => 'Unknown',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            MEETING_STATUS_SCHEDULED  => 'Scheduled',
            MEETING_STATUS_COMPLETED  => 'Completed',
            MEETING_STATUS_CANCELLED  => 'Cancelled',
            default                   => 'Unknown',
        };
    }
}
