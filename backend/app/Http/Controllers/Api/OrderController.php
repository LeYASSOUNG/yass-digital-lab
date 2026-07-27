<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with('items')->get());
    }

    public function show($id)
    {
        return response()->json(Order::with('items')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'stripe_session_id' => 'nullable|string'
        ]);

        $totalAmount = collect($validated['items'])->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        $order = Order::create([
            'email' => $validated['email'],
            'total_amount' => $totalAmount,
            'status' => 'paid',
            'stripe_session_id' => $validated['stripe_session_id'] ?? null
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_title' => $item['title'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }

        return response()->json($order->load('items'), 201);
    }

    public function downloadInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('invoice', compact('order'));
        
        return $pdf->download('facture-'.str_pad($order->id, 6, '0', STR_PAD_LEFT).'.pdf');
    }
}
