<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $requestCode,
        public string $userEmail,
        public string $userName = 'AgriCo User',
        public string $cancelUrl = '',
        public ?string $reason = null,
        public ?string $submittedAt = null
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "We received your AgriCo account deletion request ({$this->requestCode})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion-requested',
            with: [
                'requestCode' => $this->requestCode,
                'userEmail'   => $this->userEmail,
                'userName'    => $this->userName,
                'cancelUrl'   => $this->cancelUrl,
                'reason'      => $this->reason,
                'submittedAt' => $this->submittedAt ?: now()->format('M d, Y h:i A'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
