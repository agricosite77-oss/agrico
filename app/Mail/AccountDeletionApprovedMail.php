<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionApprovedMail extends Mailable
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
            subject: 'Your AgriCo account has been permanently deleted',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion-approved',
            with: [
                'requestCode' => $this->requestCode,
                'userEmail'   => $this->userEmail,
                'userName'    => $this->userName,
                'deletedAt'   => now()->format('M d, Y h:i A'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
