<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDisapprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ?User $user = null,
        public ?string $userName = null,
        public ?string $userEmail = null,
        public ?string $adminId = null,
        public ?string $reason = null
    ) {
        $this->userName = $this->userName ?: ($this->user?->name ?? 'AgriCo Applicant');
        $this->userEmail = $this->userEmail ?: ($this->user?->email ?? '');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[AgriCo] Account Registration Update - Disapproved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-disapproved',
            with: [
                'user'      => $this->user,
                'userName'  => $this->userName,
                'userEmail' => $this->userEmail,
                'adminId'   => $this->adminId,
                'reason'    => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
