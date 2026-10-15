<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangedNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $oldEmail,
        public string $newEmail,
        public string $userName = 'AgriCo User'
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: 'Security Alert: Your AgriCo account email was updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-changed-notice',
            with: [
                'oldEmail' => $this->oldEmail,
                'newEmail' => $this->newEmail,
                'userName' => $this->userName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
