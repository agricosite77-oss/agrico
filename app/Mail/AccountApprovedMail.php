<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $adminId = null,
        public ?string $loginUrl = null
    ) {
        $this->loginUrl = $loginUrl ?: url('/login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[AgriCo] Account Registration Approved - Welcome to AgriCo!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
            with: [
                'user'     => $this->user,
                'adminId'  => $this->adminId,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
