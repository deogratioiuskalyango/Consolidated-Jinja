<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;

    const TYPE_TEXT  = 1;
    const TYPE_IMAGE = 2;
    const TYPE_FILE  = 3;
    const TYPE_AUDIO = 4;
    const TYPE_VIDEO = 5;

    protected $fillable = [
        'sender_id', 'receiver_id', 'message', 'message_type',
        'file_path', 'file_name', 'file_size', 'file_mime',
        'reply_to_id', 'is_seen', 'is_delivered',
        'deleted_for_sender', 'deleted_for_receiver',
        'reactions', 'edited_at',
    ];

    protected $casts = [
        'reactions'  => 'array',
        'edited_at'  => 'datetime',
        'created_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function sender()   { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
    public function replyTo()  { return $this->belongsTo(self::class, 'reply_to_id'); }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeVisibleFor($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('deleted_for_sender', false);
        })->orWhere(function ($q) use ($userId) {
            $q->where('receiver_id', $userId)->where('deleted_for_receiver', false);
        });
    }

    public function scopeBetween($query, int $a, int $b)
    {
        return $query->where(function ($q) use ($a, $b) {
            $q->where('sender_id', $a)->where('receiver_id', $b);
        })->orWhere(function ($q) use ($a, $b) {
            $q->where('sender_id', $b)->where('receiver_id', $a);
        });
    }

    // ── Type helpers ─────────────────────────────────────────────────────────

    public function isImage(): bool { return $this->message_type == self::TYPE_IMAGE; }
    public function isFile(): bool  { return $this->message_type == self::TYPE_FILE; }
    public function isAudio(): bool { return $this->message_type == self::TYPE_AUDIO; }
    public function isVideo(): bool { return $this->message_type == self::TYPE_VIDEO; }
    public function isText(): bool  { return $this->message_type == self::TYPE_TEXT; }

    public function fileUrl(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function humanFileSize(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }

    public function fileIconClass(): string
    {
        $mime = $this->file_mime ?? '';
        if (str_contains($mime, 'pdf'))   return 'ri-file-pdf-line';
        if (str_contains($mime, 'word') || str_contains($mime, 'doc'))  return 'ri-file-word-line';
        if (str_contains($mime, 'excel') || str_contains($mime, 'sheet')) return 'ri-file-excel-line';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar')) return 'ri-file-zip-line';
        if (str_contains($mime, 'video')) return 'ri-file-video-line';
        return 'ri-file-line';
    }

    public function hasReacted(int $userId, string $emoji): bool
    {
        return in_array($userId, $this->reactions[$emoji] ?? []);
    }
}
