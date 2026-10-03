<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Commission;
use App\Models\QuoteRequest;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class OrderPaymentService
{
    public function createPendingOrder(array $validated): Order
    {
        $totalAmount = collect($validated['items'])->sum(fn($i) => $i['price'] * $i['quantity']);

        $order = Order::create([
            'email'        => $validated['email'],
            'total_amount' => $totalAmount,
            'status'       => 'pending',
            'quote_id'     => $validated['quote_id'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id'    => $item['id'] ?? null,
                'product_title' => $item['title'],
                'price'         => $item['price'],
                'quantity'      => $item['quantity'] ?? 1,
            ]);
        }

        return $order;
    }

    public function markOrderAsPaid(Order $order): void
    {
        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);
            if ($order->quote_id) {
                QuoteRequest::where('id', $order->quote_id)->update(['status' => 'completed']);
            }
            $this->sendOrderConfirmationEmail($order);
        }
    }

    public function sendOrderConfirmationEmail(Order $order): void
    {
        $signedInvoiceUrl = URL::temporarySignedRoute(
            'orders.invoice',
            now()->addDays(30),
            ['id' => $order->id]
        );

        try {
            Mail::to($order->email)->send(new OrderConfirmation($order->load('items'), $signedInvoiceUrl));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email confirmation : ' . $e->getMessage());
        }
    }

    public function creditAffiliateCommission(Order $order, ?string $affiliateCode): void
    {
        if (empty($affiliateCode)) return;

        $affiliate = User::where('affiliate_code', trim($affiliateCode))->first();
        if (!$affiliate) return;

        if (strtolower($affiliate->email) === strtolower($order->email)) return;

        $alreadyCredited = Commission::where('order_id', $order->id)->exists();
        if ($alreadyCredited) return;

        $commissionRate = 15.00;
        $commissionAmount = round(((float) $order->total_amount) * ($commissionRate / 100), 2);

        if ($commissionAmount <= 0) return;

        Commission::create([
            'affiliate_id'      => $affiliate->id,
            'order_id'          => $order->id,
            'affiliate_code'    => $affiliate->affiliate_code,
            'order_amount'      => (float) $order->total_amount,
            'commission_rate'   => $commissionRate,
            'commission_amount' => $commissionAmount,
            'status'            => 'approved',
        ]);

        $affiliate->affiliate_balance += $commissionAmount;
        $affiliate->save();
    }
}
