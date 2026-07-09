¿<?php
namespace App\Mail;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        $total = number_format(
            $this->invoice->items->sum(fn ($item) => $item->price * $item->quantity),
            2
        );

        return new Envelope(
            subject: "Invoice #{$this->invoice->number}",
        );
    }

    public function content(): Content
    {
        $total = number_format(
            $this->invoice->items->sum(fn ($item) => $item->price * $item->quantity),
            2
        );

        return new Content(
            view: 'emails.invoice',
            with: [
                'user' => $this->user,
                'invoice' => $this->invoice,
                'total' => $total,
                'appName' => config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
