<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeletionRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', DELETION_REQUEST_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', DELETION_REQUEST_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', DELETION_REQUEST_REJECTED);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === DELETION_REQUEST_PENDING;
    }

    public function statusLabel(): string
    {
        return match ((int) $this->status) {
            DELETION_REQUEST_PENDING  => __('Pending'),
            DELETION_REQUEST_APPROVED => __('Approved'),
            DELETION_REQUEST_REJECTED => __('Rejected'),
            default                   => __('Unknown'),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ((int) $this->status) {
            DELETION_REQUEST_PENDING  => 'warning',
            DELETION_REQUEST_APPROVED => 'success',
            DELETION_REQUEST_REJECTED => 'danger',
            default                   => 'secondary',
        };
    }
}
