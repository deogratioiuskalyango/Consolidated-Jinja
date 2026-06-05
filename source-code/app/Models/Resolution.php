<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolution extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'voting_opens_at' => 'datetime',
        'voting_closes_at' => 'datetime',
        'passed_at' => 'datetime',
    ];

    public function votes()
    {
        return $this->hasMany(ResolutionVote::class, 'resolution_id');
    }

    public function documents()
    {
        return $this->hasMany(GovernanceDocument::class, 'resolution_id');
    }

    public function meeting()
    {
        return $this->belongsTo(GovernanceMeeting::class, 'meeting_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            RESOLUTION_STATUS_DRAFT  => 'Draft',
            RESOLUTION_STATUS_OPEN   => 'Open',
            RESOLUTION_STATUS_CLOSED => 'Closed',
            RESOLUTION_STATUS_PASSED => 'Passed',
            RESOLUTION_STATUS_FAILED => 'Failed',
            default                  => 'Unknown',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ((int) $this->type) {
            1 => 'Financial',
            2 => 'Board Resolution',
            3 => 'Property Acquisition',
            4 => 'Dividend Declaration',
            5 => 'Share Transfer',
            6 => 'Policy Change',
            7 => 'Director Appointment',
            8 => 'Other',
            default => 'Unknown',
        };
    }

    public function getIsOpenAttribute(): bool
    {
        $now = now();
        return (int) $this->status === RESOLUTION_STATUS_OPEN
            && $this->voting_opens_at
            && $this->voting_closes_at
            && $now->between($this->voting_opens_at, $this->voting_closes_at);
    }

    public function computeResults(): void
    {
        $votes = $this->votes();
        $total = $votes->count();

        if ($total === 0) {
            $this->for_percentage     = 0;
            $this->against_percentage = 0;
            $this->abstain_percentage = 0;
        } else {
            $this->for_percentage     = round($votes->where('vote', VOTE_FOR)->count() / $total * 100, 2);
            $this->against_percentage = round($votes->where('vote', VOTE_AGAINST)->count() / $total * 100, 2);
            $this->abstain_percentage = round($votes->where('vote', VOTE_ABSTAIN)->count() / $total * 100, 2);
        }

        $this->save();
    }
}
