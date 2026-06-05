<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';
    protected $fillable = [
        'owner_user_id', 'user_id', 'role_slug', 'title', 'message',
        'type', 'icon', 'action_url', 'is_read', 'is_broadcast',
    ];
    protected $casts = ['is_read' => 'boolean', 'is_broadcast' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }

    public static function send(int $ownerUserId, ?int $userId, string $roleSlug, string $title, string $message, string $type = 'info', ?string $actionUrl = null): self
    {
        return static::create([
            'owner_user_id' => $ownerUserId,
            'user_id'       => $userId,
            'role_slug'     => $roleSlug,
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'icon'          => match($type) { 'success'=>'ri-checkbox-circle-line','warning'=>'ri-alert-line','danger'=>'ri-error-warning-line', default=>'ri-notification-3-line' },
            'action_url'    => $actionUrl,
            'is_read'       => false,
            'is_broadcast'  => is_null($userId),
        ]);
    }

    public static function forUser(int $userId, string $roleSlug, int $ownerUserId)
    {
        return static::where(function($q) use ($userId, $roleSlug, $ownerUserId) {
            $q->where('user_id', $userId)
              ->orWhere(function($q2) use ($roleSlug, $ownerUserId) {
                  $q2->whereNull('user_id')
                     ->where('role_slug', $roleSlug)
                     ->where('owner_user_id', $ownerUserId);
              });
        })->orderByDesc('created_at');
    }

    public function scopeUnread($q) { return $q->where('is_read', false); }
}
