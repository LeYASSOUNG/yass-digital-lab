<?php

/**
 * ============================================================
 * OrderConfirmation Mailable — Yass Digital Lab
 * ============================================================
 * Classe Mailable Laravel pour l'envoi automatique de l'email
 * de confirmation de commande après un paiement Stripe réussi.
 *
 * Utilisé dans : OrderController::store()
 * Template Blade : resources/views/emails/order-confirmation.blade.php
 *
 * Données transmises au template :
 *   - $order       : Modèle Order avec ses articles (items chargés)
 *   - $orderNumber : Numéro formaté sur 6 chiffres (ex: FA-000042)
 * ============================================================
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderConfirmation extends Mailable
{
    // Traits pour la mise en file d'attente (queue) et la sérialisation des modèles Eloquent
    use Queueable, SerializesModels;

    /**
     * Constructeur — Injection de la commande à confirmer.
     *
     * L'utilisation de `public` sur $order expose automatiquement
     * la variable au template Blade sans avoir besoin de with().
     *
     * @param  Order $order  La commande dont il faut envoyer la confirmation
     */
    public function __construct(public Order $order)
    {}

    /**
     * Définit l'enveloppe de l'email (expéditeur, objet du message).
     *
     * Le numéro de commande est inclus dans l'objet pour faciliter
     * la reconnaissance immédiate par le client dans sa boîte mail.
     * @return Envelope Objet contenant les métadonnées de l'email
     */
    public function envelope(): Envelope
    {
        $orderNum = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        return new Envelope(
            subject: "✅ Confirmation de votre commande #FA-{$orderNum} — Yass Digital Lab",
        );
    }

    /**
     * Définit le contenu de l'email (template Blade + variables).
     *
     * Utilise le template HTML responsive : emails/order-confirmation.blade.php
     * Deux variables sont passées au template :
     *   - $order       : Objet commande complet avec les articles
     *   - $orderNumber : Numéro formaté pour l'affichage (ex: FA-000042)
     *
     * @return Content  Objet contenant la vue et les données du template
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',  // Template Blade de l'email
            with: [
                'order'       => $this->order,
                'orderNumber' => 'FA-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
            ]
        );
    }
}
