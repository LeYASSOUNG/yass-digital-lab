<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_session_creates_pending_order()
    {
        $response = $this->postJson('/api/create-checkout-session', [
            'email' => 'buyer@example.com',
            'items' => [
                ['title' => 'Ebook IA', 'price' => 19.99, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'url', 'order_id']);

        $this->assertDatabaseHas('orders', [
            'email'  => 'buyer@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_stripe_webhook_completed_event_marks_order_as_paid()
    {
        $order = Order::create([
            'email'        => 'buyer@example.com',
            'total_amount' => 19.99,
            'status'       => 'pending',
            'stripe_session_id' => 'cs_test_mock_123',
        ]);

        $webhookPayload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_mock_123',
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/stripe/webhook', $webhookPayload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'paid',
        ]);
    }
}
