<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_process_mobile_money_checkout(): void
    {
        $response = $this->postJson('/api/mobile-money/checkout', [
            'email'    => 'client@example.com',
            'phone'    => '+225 07 00 00 00 00',
            'provider' => 'wave',
            'amount'   => 39.00,
            'items'    => [
                ['title' => 'Pack IA Marketing', 'price' => 39.00, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'paid']);
        $this->assertDatabaseHas('orders', ['email' => 'client@example.com', 'total_amount' => 39.00]);
    }

    public function test_can_fetch_order_by_session_id(): void
    {
        $order = Order::create([
            'email'             => 'test@example.com',
            'total_amount'      => 49.00,
            'status'            => 'paid',
            'stripe_session_id' => 'momo_wave_1234567890',
        ]);

        $response = $this->getJson("/api/orders/by-session/momo_wave_1234567890");

        $response->assertStatus(200);
        $response->assertJsonFragment(['email' => 'test@example.com']);
    }
}
