<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Configuration temporaire avec une fausse clé pour le développement
        // En production, utiliser env('STRIPE_SECRET')
        Stripe::setApiKey(env('STRIPE_SECRET', 'sk_test_fake_key_123456789'));

        $lineItems = [];

        foreach ($request->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['title'],
                    ],
                    'unit_amount' => intval($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/dashboard?success=true',
                'cancel_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/checkout?canceled=true',
            ]);

            return response()->json(['id' => $session->id, 'url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
