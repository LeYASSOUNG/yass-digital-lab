<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $invoiceUrl = null
    ) {}

    public function envelope(): Envelope
    {
        $orderNum = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        return new Envelope(
            subject: "✅ Confirmation de votre commande #FA-{$orderNum} — Yass Digital Lab",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order'       => $this->order,
                'orderNumber' => 'FA-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
                'invoiceUrl'  => $this->invoiceUrl,
            ]
        );
    }

    public function attachments(): array
    {
        $orderNum = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $pdf = Pdf::loadView('invoice', ['order' => $this->order]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), "Facture-FA-{$orderNum}.pdf")
                    ->withMime('application/pdf'),
        ];
    }
}
