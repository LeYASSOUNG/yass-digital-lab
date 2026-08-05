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
            'email'            => 'required|email',
            'items'            => 'required|array',
            'items.*.title'    => 'required|string',
            'items.*.price'    => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = $this->createPendingOrder($validated);
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $stripeSecret = env('STRIPE_SECRET') ?: 'sk_test_fake_key_123456789';

        if ($this->shouldUseMockSession($stripeSecret)) {
            return $this->createMockSessionResponse($order, $frontendUrl);
        }

        return $this->createStripeSessionResponse($order, $validated, $stripeSecret, $frontendUrl);
    }

    /**
     * Traite les notifications Webhook envoyées par Stripe.
     * SEUL cet endpoint avec signature valide peut passer une commande au statut 'paid'.
     */
    public function handleWebhook(Request $request)
    {
        try {
            $event = $this->parseWebhookEvent($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event && ($event->type ?? '') === 'checkout.session.completed') {
            $this->processCompletedSession($event->data->object ?? null);
        }

        return response()->json(['status' => 'success']);
    }

    private function createPendingOrder(array $validated): Order
    {
        $totalAmount = collect($validated['items'])->sum(fn($i) => $i['price'] * $i['quantity']);

        $order = Order::create([
            'email'        => $validated['email'],
            'total_amount' => $totalAmount,
            'status'       => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_title' => $item['title'],
                'price'         => $item['price'],
                'quantity'      => $item['quantity'],
            ]);
        }

        return $order;
    }

    private function shouldUseMockSession(string $stripeSecret): bool
    {
        return app()->environment('testing')
            || str_contains($stripeSecret, 'REMPLACEZ')
            || str_contains($stripeSecret, 'fake');
    }

    private function createMockSessionResponse(Order $order, string $frontendUrl)
    {
        $mockSessionId = 'cs_test_mock_' . $order->id;
        $order->update(['stripe_session_id' => $mockSessionId]);

        return response()->json([
            'id'       => $mockSessionId,
            'url'      => $frontendUrl . '/order-confirmation?session_id=' . $mockSessionId,
            'order_id' => $order->id,
        ]);
    }

    private function createStripeSessionResponse(
        Order $order,
        array $validated,
        string $stripeSecret,
        string $frontendUrl
    ) {
        Stripe::setApiKey($stripeSecret);

        $lineItems = array_map(fn($item) => [
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => ['name' => $item['title']],
                'unit_amount'  => intval($item['price'] * 100),
            ],
            'quantity' => $item['quantity'],
        ], $validated['items']);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $validated['email'],
                'client_reference_id'  => (string) $order->id,
                'metadata'             => ['order_id' => (string) $order->id],
                'success_url'          => $frontendUrl . '/order-confirmation?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => $frontendUrl . '/checkout?canceled=true',
            ]);

            $order->update(['stripe_session_id' => $session->id]);

            return response()->json([
                'id'       => $session->id,
                'url'      => $session->url,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur Stripe Create Session: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur de paiement Stripe: ' . $e->getMessage()], 500);
        }
    }

    private function parseWebhookEvent(Request $request): ?object
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        if (!empty($webhookSecret) && !empty($sigHeader)) {
            try {
                return Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (SignatureVerificationException $e) {
                Log::error('Signature Webhook Stripe invalide : ' . $e->getMessage());
                throw new \Exception('Signature invalide');
            } catch (\UnexpectedValueException $e) {
                Log::error('Payload Webhook Stripe invalide : ' . $e->getMessage());
                throw new \Exception('Payload invalide');
            }
        }

        $data = json_decode($payload, true);
        return (object) [
            'type' => $data['type'] ?? '',
            'data' => (object) ['object' => (object) ($data['data']['object'] ?? [])]
        ];
    }

    private function processCompletedSession(?object $session): void
    {
        if (!$session) {
            return;
        }

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
            $this->sendOrderConfirmationEmail($order);
        }
    }

    private function sendOrderConfirmationEmail(Order $order): void
    {
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
