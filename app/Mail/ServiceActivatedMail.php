<?php
namespace App\Mail;

use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Service $service,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Activated',
        );
    }

    public function content(): Content
    {
        $productName = $this->service->product?->name ?? 'Your service';

        return new Content(
            view: 'emails.service-activated',
            with: [
                'user' => $this->user,
                'service' => $this->service,
                'productName' => $productName,
                'appName' => config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
