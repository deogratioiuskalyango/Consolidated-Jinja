<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareholderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $notifTitle,
        public string $notifMessage,
        public string $actionUrl = '',
        public string $actionLabel = 'View Details',
        public string $notifType = 'general',
        public string $recipientName = '',
    ) {}

    public function build(): static
    {
        return $this->subject($this->notifTitle)
            ->view('emails.shareholder-notification');
    }
}
