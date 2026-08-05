<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use Illuminate\Support\Facades\URL;

class InvoiceSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_unsigned_invoice_download_returns_403()
    {
        $order = Order::create([
            'email'        => 'client@example.com',
            'total_amount' => 49.99,
            'status'       => 'paid',
        ]);

        // Téléchargement sans signature ni auth -> 403 Forbidden (Anti-IDOR)
        $response = $this->getJson("/api/orders/{$order->id}/invoice");
        $response->assertStatus(403);
    }

    public function test_signed_invoice_url_allows_download()
    {
        $order = Order::create([
            'email'        => 'client@example.com',
            'total_amount' => 49.99,
            'status'       => 'paid',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'orders.invoice',
            now()->addMinutes(30),
            ['id' => $order->id]
        );

        $response = $this->get($signedUrl);
        $response->assertStatus(200);
    }
}
