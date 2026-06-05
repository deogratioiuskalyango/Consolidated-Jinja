<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    public function fromShareholder()
    {
        return $this->belongsTo(Shareholder::class, 'from_shareholder_id');
    }

    public function toShareholder()
    {
        return $this->belongsTo(Shareholder::class, 'to_shareholder_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function legalDocument()
    {
        return $this->belongsTo(FileManager::class, 'legal_document_file_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            TRANSFER_STATUS_PENDING  => 'Pending',
            TRANSFER_STATUS_APPROVED => 'Approved',
            TRANSFER_STATUS_REJECTED => 'Rejected',
            default                  => 'Unknown',
        };
    }
}
