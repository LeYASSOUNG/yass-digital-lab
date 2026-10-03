<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function createStripeSession(Order $order, array $validated, string $frontendUrl): array
    {
        $stripeSecret = env('STRIPE_SECRET') ?: 'sk_test_fake_key_123456789';
        
        if ($this->shouldUseMockSession($stripeSecret)) {
            return $this->createMockSession($order, $frontendUrl);
        }

        Stripe::setApiKey($stripeSecret);

        $lineItems = array_map(fn($item) => [
            'price_data' => [
                'currency'     => 'xof',
                'product_data' => ['name' => $item['title']],
                'unit_amount'  => intval($item['price']), // XOF is a zero-decimal currency
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

            return [
                'success' => true,
                'id'      => $session->id,
                'url'     => $session->url,
                'order_id'=> $order->id,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur Stripe Create Session: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de paiement Stripe: ' . $e->getMessage()
            ];
        }
    }

    public function parseWebhookEvent(string $payload, ?string $sigHeader): ?object
    {
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

    private function shouldUseMockSession(string $stripeSecret): bool
    {
        return app()->environment('testing')
            || str_contains($stripeSecret, 'REMPLACEZ')
            || str_contains($stripeSecret, 'fake');
    }

    private function createMockSession(Order $order, string $frontendUrl): array
    {
        $mockSessionId = 'cs_test_mock_' . $order->id;
        $order->update(['stripe_session_id' => $mockSessionId]);

        return [
            'success' => true,
            'id'      => $mockSessionId,
            'url'     => $frontendUrl . '/order-confirmation?session_id=' . $mockSessionId,
            'order_id'=> $order->id,
        ];
    }
}
