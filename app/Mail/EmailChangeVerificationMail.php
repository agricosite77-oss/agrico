<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $newEmail,
        public string $userName = 'AgriCo User'
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'agricosite77@gmail.com');
        $fromName = config('mail.from.name', 'AgriCo Support');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "Your AgriCo Email Verification Code: {$this->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-change-code',
            with: [
                'code'     => $this->code,
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
