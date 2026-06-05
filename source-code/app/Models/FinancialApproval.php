<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'deadline_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function actions()
    {
        return $this->hasMany(FinancialApprovalAction::class, 'financial_approval_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function documents()
    {
        return $this->hasMany(GovernanceDocument::class, 'financial_approval_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            APPROVAL_STATUS_PENDING  => 'Pending',
            APPROVAL_STATUS_APPROVED => 'Approved',
            APPROVAL_STATUS_REJECTED => 'Rejected',
            APPROVAL_STATUS_EXPIRED  => 'Expired',
            default                  => 'Unknown',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ((int) $this->priority) {
            1 => 'Urgent',
            2 => 'Normal',
            3 => 'Low',
            default => 'Normal',
        };
    }

    public function computeStatus(int $ownerUserId): void
    {
        $totalShareholders = Shareholder::where('owner_user_id', $ownerUserId)
            ->where('status', SHAREHOLDER_STATUS_ACTIVE)
            ->count();

        if ($totalShareholders === 0) {
            return;
        }

        $approvals = $this->actions()->where('action', APPROVAL_ACTION_APPROVE)->count();
        $rejections = $this->actions()->where('action', APPROVAL_ACTION_REJECT)->count();

        $threshold = $this->approval_threshold ?? 51;
        $approvalPercentage = ($approvals / $totalShareholders) * 100;

        if ($approvalPercentage >= $threshold) {
            $this->status = APPROVAL_STATUS_APPROVED;
            $this->approved_at = now();
        } elseif ($rejections > ($totalShareholders - $totalShareholders * ($threshold / 100))) {
            $this->status = APPROVAL_STATUS_REJECTED;
        } else {
            $this->status = APPROVAL_STATUS_PENDING;
        }

        $this->save();
    }
}
