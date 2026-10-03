<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\QuoteRequest;

/**
 * Mailable envoyé automatiquement au client lorsque son devis est accepté par l'admin.
 */
class QuoteAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote
    ) {}

    public function envelope(): Envelope
    {
        $refNum = 'DEV-' . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT);
        return new Envelope(
            subject: "✅ Votre Devis #{$refNum} a été accepté — Yass Digital Lab",
        );
    }

    public function content(): Content
    {
        $raw    = $this->quote->amount ?: ($this->quote->budget ?: '');
        $digits = preg_replace('/[^\d]/', '', $raw);
        $displayAmount = ($digits !== '')
            ? number_format((float)$digits, 0, '.', ' ') . ' FCFA'
            : ($raw ?: 'Sur Devis');

        return new Content(
            view: 'emails.quote_accepted',
            with: [
                'quote'         => $this->quote,
                'refNum'        => 'DEV-' . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT),
                'displayAmount' => $displayAmount,
                'trackingUrl'   => rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/suivi-devis?ref=' . $this->quote->id . '&email=' . urlencode($this->quote->email),
                'whatsappUrl'   => 'https://wa.me/22549679002?text=' . urlencode(
                    'Bonjour Yass Digital Lab, mon devis DEV-' . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT) . ' a été accepté. Je souhaite démarrer le projet.'
                ),
            ]
        );
    }
}
