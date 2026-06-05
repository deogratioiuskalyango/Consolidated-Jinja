<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareholderNotificationMail;

class ShareholderNotification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class, 'shareholder_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Labels shown in email CTA based on notification type.
     */
    private static function actionLabel(string $type): string
    {
        return match ($type) {
            'meeting'          => 'View Meeting Details',
            'approval_request' => 'Review & Approve',
            'new_resolution'   => 'Vote Now',
            'vote_reminder'    => 'Cast Your Vote',
            'dividend'         => 'View Dividend Details',
            'document'         => 'View Document',
            default            => 'View Details',
        };
    }

    /**
     * Send an in-app notification + email to a single shareholder.
     */
    public static function notifyOne(
        Shareholder $shareholder,
        string $type,
        string $title,
        string $message,
        string $actionUrl = '',
        mixed $subject = null
    ): static {
        $notif = static::create([
            'owner_user_id'  => $shareholder->owner_user_id,
            'shareholder_id' => $shareholder->id,
            'type'           => $type,
            'title'          => $title,
            'message'        => $message,
            'action_url'     => $actionUrl,
            'subject_type'   => $subject ? get_class($subject) : null,
            'subject_id'     => $subject?->id,
            'is_read'        => false,
            'read_at'        => null,
        ]);

        static::deliverEmail($shareholder, $type, $title, $message, $actionUrl);
        static::deliverWhatsApp($shareholder, $title, $message, $actionUrl);

        return $notif;
    }

    /**
     * Send in-app + email + WhatsApp to every active shareholder for this owner.
     */
    public static function notifyAll(
        int $ownerUserId,
        string $type,
        string $title,
        string $message,
        string $actionUrl = '',
        mixed $subject = null
    ): void {
        $shareholders = Shareholder::where('owner_user_id', $ownerUserId)
            ->where('status', SHAREHOLDER_STATUS_ACTIVE)
            ->with('user')
            ->get();

        foreach ($shareholders as $shareholder) {
            static::create([
                'owner_user_id'  => $ownerUserId,
                'shareholder_id' => $shareholder->id,
                'type'           => $type,
                'title'          => $title,
                'message'        => $message,
                'action_url'     => $actionUrl,
                'subject_type'   => $subject ? get_class($subject) : null,
                'subject_id'     => $subject?->id,
                'is_read'        => false,
                'read_at'        => null,
            ]);

            static::deliverEmail($shareholder, $type, $title, $message, $actionUrl);
            static::deliverWhatsApp($shareholder, $title, $message, $actionUrl);
        }
    }

    // ── Private delivery helpers ─────────────────────────────────────────────

    private static function deliverEmail(
        Shareholder $shareholder,
        string $type,
        string $title,
        string $message,
        string $actionUrl
    ): void {
        $email = $shareholder->user->email ?? null;
        if (! $email) return;

        try {
            $mailable = new ShareholderNotificationMail(
                notifTitle:    $title,
                notifMessage:  $message,
                actionUrl:     $actionUrl,
                actionLabel:   static::actionLabel($type),
                notifType:     $type,
                recipientName: trim(($shareholder->user->first_name ?? '') . ' ' . ($shareholder->user->last_name ?? '')),
            );
            Mail::mailer('failover')->to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning("ShareholderNotificationMail failed for {$email}: " . $e->getMessage());
        }
    }

    private static function deliverWhatsApp(
        Shareholder $shareholder,
        string $title,
        string $message,
        string $actionUrl
    ): void {
        $phone = $shareholder->user->whatsapp_number ?? null;
        if (! $phone) return;

        try {
            $waText = "🔔 *{$title}*\n\n{$message}";
            if ($actionUrl) $waText .= "\n\n🔗 {$actionUrl}";
            app(\App\Services\WhatsAppService::class)->send($phone, $waText);
        } catch (\Throwable $e) {
            Log::warning("WhatsApp notification failed for {$phone}: " . $e->getMessage());
        }
    }
}
