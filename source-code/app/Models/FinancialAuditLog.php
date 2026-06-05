<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'owner_user_id', 'user_id', 'action', 'auditable_type', 'auditable_id',
        'before', 'after', 'description', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'before'     => 'array',
        'after'      => 'array',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function record(
        string $action,
        int $ownerUserId,
        $auditable = null,
        array $before = [],
        array $after = [],
        string $description = ''
    ): void {
        static::create([
            'owner_user_id'  => $ownerUserId,
            'user_id'        => auth()->id() ?? 0,
            'action'         => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id'   => $auditable?->id,
            'before'         => $before ?: null,
            'after'          => $after  ?: null,
            'description'    => $description,
            'ip_address'     => request()->ip(),
            'user_agent'     => substr(request()->userAgent() ?? '', 0, 255),
            'created_at'     => now(),
        ]);
    }
}
