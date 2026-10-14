<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoilPinDisapprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName = 'AgriCo Contributor',
        public string|int $scanId = '1042',
        public string $barangay = 'Centro (Poblacion)',
        public ?string $reason = 'Ambiguous soil sample illumination or coordinate mismatch with barangay boundary.'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[AgriCo] Notice Regarding Soil Pin Submission #{$this->scanId}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soil-pin-disapproved',
            with: [
                'userName' => $this->userName,
                'scanId'   => $this->scanId,
                'barangay' => $this->barangay,
                'reason'   => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
