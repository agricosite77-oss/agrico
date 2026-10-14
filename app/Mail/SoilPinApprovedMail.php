<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoilPinApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName = 'AgriCo Contributor',
        public string|int $scanId = '1042',
        public string $barangay = 'Centro (Poblacion)',
        public string|float $latitude = '18.1965',
        public string|float $longitude = '121.6621',
        public ?string $munsellCode = '10YR 4/3',
        public ?string $soilName = 'Brown Loam'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[AgriCo] Your Soil Pin #{$this->scanId} Has Been Approved & Published!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soil-pin-approved',
            with: [
                'userName'    => $this->userName,
                'scanId'      => $this->scanId,
                'barangay'    => $this->barangay,
                'latitude'    => $this->latitude,
                'longitude'   => $this->longitude,
                'munsellCode' => $this->munsellCode,
                'soilName'    => $this->soilName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
