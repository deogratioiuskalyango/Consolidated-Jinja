<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shareholder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shareClass()
    {
        return $this->belongsTo(ShareClass::class, 'share_class_id');
    }

    public function votes()
    {
        return $this->hasMany(ResolutionVote::class, 'shareholder_id');
    }

    public function approvalActions()
    {
        return $this->hasMany(FinancialApprovalAction::class, 'shareholder_id');
    }

    public function notifications()
    {
        return $this->hasMany(ShareholderNotification::class, 'shareholder_id');
    }

    public function dividendPayments()
    {
        return $this->hasMany(DividendPayment::class, 'shareholder_id');
    }

    public function loginDevices()
    {
        return $this->hasMany(ShareholderLoginDevice::class, 'shareholder_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(GovernanceMeeting::class, 'meeting_attendees', 'shareholder_id', 'meeting_id');
    }

    public function transfersFrom()
    {
        return $this->hasMany(ShareTransfer::class, 'from_shareholder_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(ShareTransfer::class, 'to_shareholder_id');
    }

    public function vestingSchedules()
    {
        return $this->hasMany(VestingSchedule::class, 'shareholder_id');
    }

    public function delegationsGiven()
    {
        return $this->hasMany(VoteDelegation::class, 'delegator_shareholder_id');
    }

    public function delegationsReceived()
    {
        return $this->hasMany(VoteDelegation::class, 'delegatee_shareholder_id');
    }

    // ── Governance helpers ───────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));
    }

    public function getEffectiveVotingWeightAttribute(): float
    {
        if (!$this->shareClass || !$this->shareClass->voting_rights) return 0;
        return $this->total_shares * ($this->shareClass->voting_multiplier ?? 1);
    }

    public function hasPermission(string $permissionKey): bool
    {
        return $this->shareClass?->hasPermission($permissionKey) ?? false;
    }

    public function getClassCodeAttribute(): ?string
    {
        return $this->shareClass?->class_code;
    }

    public function canVoteOnResolutionType(int $resolutionType): bool
    {
        $class = $this->shareClass;
        if (!$class || !$class->voting_rights) return false;
        $allowed = $class->allowed_resolution_types;
        return empty($allowed) || in_array($resolutionType, $allowed);
    }

    public function getOwnerAttribute()
    {
        return User::find($this->owner_user_id);
    }

    public function getShareholderIdPaddedAttribute(): string
    {
        return 'SH-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
