<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class GovernanceAuditLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function delete(): bool|null
    {
        throw new LogicException('Audit logs are immutable');
    }

    public static function record(
        string $action,
        mixed $subject = null,
        array $data = [],
        string $description = ''
    ): static {
        return static::create([
            'action'         => $action,
            'subject_type'   => $subject ? get_class($subject) : null,
            'subject_id'     => $subject?->id,
            'description'    => $description,
            'old_values'     => $data['old'] ?? null,
            'new_values'     => $data['new'] ?? null,
            'user_id'        => auth()->id(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'occurred_at'    => now(),
        ]);
    }
}
