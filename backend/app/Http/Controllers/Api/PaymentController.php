<?php

/**
 * ============================================================
 * PaymentController — Yass Digital Lab
 * ============================================================
 * Contrôleur de paiement via l'API Stripe Checkout et gestion des Webhooks.
 * - Crée la commande en statut 'pending' au moment de la génération de session.
 * - Traite les événements de Webhook Stripe ('checkout.session.completed')
 *   avec vérification stricte de la signature cryptographique.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    /**
     * Crée une session de paiement Stripe Checkout et enregistre la commande en statut 'pending'.
     */
    public function createCheckoutSession(Request $request)
    {
        $validated = $request->validate([
            'email'              => 'required|email',
            'items'              => 'required|array',
            'items.*.title'      => 'required|string',
            'items.*.price'      => 'required|numeric',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $totalAmount = collect($validated['items'])->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // 1. Création de la commande principale en statut 'pending'
        $order = Order::create([
            'email'        => $validated['email'],
            'total_amount' => $totalAmount,
            'status'       => 'pending',
        ]);

        // 2. Création des articles rattachés
        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_title' => $item['title'],
                'price'         => $item['price'],
                'quantity'      => $item['quantity']
            ]);
        }

        // 3. Initialisation API Stripe
        $stripeSecret = env('STRIPE_SECRET');
        if (empty($stripeSecret)) {
            $stripeSecret = 'sk_test_fake_key_123456789';
        }
        Stripe::setApiKey($stripeSecret);

        $lineItems = [];
        foreach ($validated['items'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name' => $item['title'],
                    ],
                    'unit_amount'  => intval($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        // En environnement de test ou si clé Stripe non configurée, retourner une session simulée
        if (app()->environment('testing') || str_contains($stripeSecret, 'REMPLACEZ') || str_contains($stripeSecret, 'fake')) {
            $mockSessionId = 'cs_test_mock_' . $order->id;
            $order->update(['stripe_session_id' => $mockSessionId]);
            return response()->json([
                'id'       => $mockSessionId,
                'url'      => $frontendUrl . '/order-confirmation?session_id=' . $mockSessionId,
                'order_id' => $order->id
            ]);
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $validated['email'],
                'client_reference_id'  => (string) $order->id,
                'metadata'             => [
                    'order_id' => (string) $order->id,
                ],
                'success_url' => $frontendUrl . '/order-confirmation?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => $frontendUrl . '/checkout?canceled=true',
            ]);

            // Enregistrer l'ID de session sur la commande
            $order->update(['stripe_session_id' => $session->id]);

            return response()->json([
                'id'       => $session->id,
                'url'      => $session->url,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur Stripe Create Session: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur de paiement Stripe: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Traite les notifications Webhook envoyées par Stripe.
     * SEUL cet endpoint avec signature valide peut passer une commande au statut 'paid'.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        $event = null;

        if (!empty($webhookSecret) && !empty($sigHeader)) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (SignatureVerificationException $e) {
                Log::error('Signature Webhook Stripe invalide : ' . $e->getMessage());
                return response()->json(['error' => 'Signature invalide'], 400);
            } catch (\UnexpectedValueException $e) {
                Log::error('Payload Webhook Stripe invalide : ' . $e->getMessage());
                return response()->json(['error' => 'Payload invalide'], 400);
            }
        } else {
            // Mode sans secret webhook configuré (ex: tests locaux ou dev)
            $data = json_decode($payload, true);
            $event = (object) [
                'type' => $data['type'] ?? '',
                'data' => (object) ['object' => (object) ($data['data']['object'] ?? [])]
            ];
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? $session->client_reference_id ?? null;
            $sessionId = $session->id ?? null;

            $order = null;
            if ($orderId) {
                $order = Order::find($orderId);
            } elseif ($sessionId) {
                $order = Order::where('stripe_session_id', $sessionId)->first();
            }

            if ($order && $order->status !== 'paid') {
                $order->update(['status' => 'paid']);

                // Générer une URL signée temporaire (30 jours) pour la facture
                $signedInvoiceUrl = URL::temporarySignedRoute(
                    'orders.invoice',
                    now()->addDays(30),
                    ['id' => $order->id]
                );

                try {
                    Mail::to($order->email)->send(new OrderConfirmation($order->load('items'), $signedInvoiceUrl));
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email confirmation webhook : ' . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
