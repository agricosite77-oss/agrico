<?php

namespace App\Mail;

use App\Models\LocationMapping;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoilPinSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LocationMapping $location,
        public ?User $user = null,
        public string $platform = 'application'
    ) {}

    public function envelope(): Envelope
    {
        $brgy = $this->location->barangay ?: 'Lal-lo';
        return new Envelope(
            subject: "[AgriCo] Soil Location Pin Submitted: Brgy. {$brgy}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soil-pin-submitted',
            with: [
                'location' => $this->location,
                'user'     => $this->user,
                'platform' => $this->platform,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
