<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GovernanceDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function file()
    {
        return $this->belongsTo(FileManager::class, 'file_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function meeting()
    {
        return $this->belongsTo(GovernanceMeeting::class, 'meeting_id');
    }

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id');
    }

    public function getFileUrlAttribute(): string
    {
        return $this->file->FileUrl ?? asset('assets/images/no-image.jpg');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
