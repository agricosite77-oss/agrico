<?php

namespace App\Mail;

use App\Models\SoilScan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoilScanModeratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SoilScan $scan,
        public string $status, // 'Verified' or 'Rejected'
        public ?User $user = null,
        public ?string $adminId = null
    ) {}

    public function envelope(): Envelope
    {
        $statusVerb = $this->status === 'Verified' ? 'Approved & Published' : 'Disapproved / Hidden';
        $scanId = $this->scan->soil_scan_id;
        return new Envelope(
            subject: "[AgriCo] Soil Scan #{$scanId} {$statusVerb}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soil-scan-moderated',
            with: [
                'scan'    => $this->scan,
                'status'  => $this->status,
                'user'    => $this->user,
                'adminId' => $this->adminId,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
