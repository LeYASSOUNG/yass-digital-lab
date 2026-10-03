<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\QuoteRequest;

/**
 * Email envoyé immédiatement au client dès que sa demande de devis est reçue.
 * Confirme la réception et indique le délai de traitement.
 */
class QuoteReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote
    ) {}

    public function envelope(): Envelope
    {
        $refNum = 'DEV-' . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT);
        return new Envelope(
            subject: "📋 Votre demande de devis #{$refNum} a bien été reçue — Yass Digital Lab",
        );
    }

    public function content(): Content
    {
        $raw    = $this->quote->amount ?: ($this->quote->budget ?: '');
        $digits = preg_replace('/[^\d]/', '', $raw);
        $displayBudget = ($digits !== '')
            ? number_format((float)$digits, 0, '.', ' ') . ' FCFA'
            : ($raw ?: 'À définir');

        $frontendUrl = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/');

        return new Content(
            view: 'emails.quote_received',
            with: [
                'quote'         => $this->quote,
                'refNum'        => 'DEV-' . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT),
                'displayBudget' => $displayBudget,
                'trackingUrl'   => $frontendUrl . '/suivi-devis?ref=' . $this->quote->id . '&email=' . urlencode($this->quote->email),
                'whatsappUrl'   => 'https://wa.me/22549679002?text=' . urlencode(
                    "Bonjour Yass Digital Lab, j'ai soumis une demande de devis (Réf: DEV-" . str_pad($this->quote->id, 6, '0', STR_PAD_LEFT) . "). Je souhaite avoir une confirmation."
                ),
            ]
        );
    }
}
