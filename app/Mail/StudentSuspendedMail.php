<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public ?Admin $admin = null,
        public string $reason = 'Administrative policy violation',
        public ?Carbon $until = null,
        public string $suspendedBy = 'System Administrator'
    ) {}

    public function envelope(): Envelope
    {
        $studentName = $this->student->name ?: 'Student';
        return new Envelope(
            subject: "[AgriCo Alert] Student Suspended: {$studentName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-suspended',
            with: [
                'student'     => $this->student,
                'admin'       => $this->admin,
                'reason'      => $this->reason,
                'until'       => $this->until,
                'suspendedBy' => $this->suspendedBy,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
