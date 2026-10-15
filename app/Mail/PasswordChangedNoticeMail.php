<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userEmail,
        public string $userName = 'AgriCo User',
        public ?string $ipAddress = null
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: 'Security Alert: Your AgriCo account password was changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed-notice',
            with: [
                'userEmail' => $this->userEmail,
                'userName'  => $this->userName,
                'ipAddress' => $this->ipAddress,
                'changedAt' => now()->format('M d, Y h:i A'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
