<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $requestCode,
        public string $userEmail,
        public string $userName = 'AgriCo User'
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "Your AgriCo account deletion request has been cancelled ({$this->requestCode})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion-cancelled',
            with: [
                'requestCode' => $this->requestCode,
                'userEmail'   => $this->userEmail,
                'userName'    => $this->userName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
