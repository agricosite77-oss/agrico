<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $requestCode,
        public string $userEmail,
        public string $userName = 'AgriCo User',
        public ?string $adminReason = null
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "Update on your AgriCo account deletion request ({$this->requestCode})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion-rejected',
            with: [
                'requestCode' => $this->requestCode,
                'userEmail'   => $this->userEmail,
                'userName'    => $this->userName,
                'adminReason' => $this->adminReason,
                'reviewedAt'  => now()->format('M d, Y h:i A'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
