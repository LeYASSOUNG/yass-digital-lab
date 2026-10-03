<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderPaymentService;
use App\Services\StripePaymentService;
use App\Services\CinetPayService;
use App\Services\GeniusPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $orderService;
    protected $stripeService;
    protected $cinetPayService;

    public function __construct(
        OrderPaymentService $orderService,
        StripePaymentService $stripeService,
        CinetPayService $cinetPayService
    ) {
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
        $this->cinetPayService = $cinetPayService;
    }

    /**
     * Crée une session de paiement Stripe Checkout
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'            => 'required|email',
                'items'            => 'required|array',
                'items.*.id'       => 'nullable|integer',
                'items.*.title'    => 'required|string',
                'items.*.price'    => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1',
                'quote_id'         => 'nullable|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed:', $e->errors());
            throw $e;
        }

        $order = $this->orderService->createPendingOrder($validated);
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        $response = $this->stripeService->createStripeSession($order, $validated, $frontendUrl);

        if (!$response['success']) {
            return response()->json(['error' => $response['message']], 500);
        }

        return response()->json([
            'id'       => $response['id'],
            'url'      => $response['url'],
            'order_id' => $response['order_id'],
        ]);
    }

    /**
     * Traite le paiement Mobile Money Direct
     */
    public function handleMobileMoneyCheckout(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'phone'    => 'required|string',
            'provider' => 'required|string',
            'amount'   => 'required|numeric',
            'items'    => 'required|array',
            'items.*.id' => 'nullable|integer',
            'items.*.title' => 'required|string',
            'items.*.price' => 'required|numeric',
            'quote_id' => 'nullable|integer',
        ]);

        $sessionId = 'momo_' . $validated['provider'] . '_' . \Illuminate\Support\Str::random(10);
        
        $order = $this->orderService->createPendingOrder($validated);
        $order->update(['stripe_session_id' => $sessionId]);
        
        // Ajouter un token de licence bidon pour MoMo comme c'était fait avant si nécessaire
        // Mais nous avons simplifié la création de commande dans OrderPaymentService.

        $this->orderService->markOrderAsPaid($order);
        $this->orderService->creditAffiliateCommission($order, $request->input('affiliate_code'));

        return response()->json([
            'message'    => 'Paiement ' . strtoupper($validated['provider']) . ' validé avec succès !',
            'order_id'   => $order->id,
            'session_id' => $sessionId,
            'status'     => 'paid',
        ]);
    }

    /**
     * Webhook Stripe
     */
    public function handleWebhook(Request $request)
    {
        try {
            $event = $this->stripeService->parseWebhookEvent($request->getContent(), $request->header('Stripe-Signature'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event && ($event->type ?? '') === 'checkout.session.completed') {
            $session = $event->data->object ?? null;
            if ($session) {
                $orderId = $session->metadata->order_id ?? $session->client_reference_id ?? null;
                $sessionId = $session->id ?? null;
                
                $order = null;
                if ($orderId) {
                    $order = Order::find($orderId);
                } elseif ($sessionId) {
                    $order = Order::where('stripe_session_id', $sessionId)->first();
                }

                if ($order) {
                    $this->orderService->markOrderAsPaid($order);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * CINETPAY
     */
    public function initiateCinetPay(Request $request)
    {
        $validated = $request->validate([
            'email'            => 'required|email',
            'amount'           => 'required|numeric',
            'items'            => 'required|array',
            'items.*.id'       => 'nullable|integer',
            'items.*.title'    => 'required|string',
            'items.*.price'    => 'required|numeric',
            'affiliate_code'   => 'nullable|string',
        ]);

        $order = $this->orderService->createPendingOrder($validated);
        
        $response = $this->cinetPayService->initiatePayment($order, $validated['email']);

        if ($response['success']) {
            return response()->json([
                'payment_url' => $response['payment_url'],
                'order_id' => $response['order_id']
            ]);
        }

        return response()->json(['error' => $response['message']], 400);
    }

    public function cinetpayNotify(Request $request)
    {
        $cpm_trans_id = $request->input('cpm_trans_id');
        
        if (!$cpm_trans_id) {
            return response()->json(['error' => 'cpm_trans_id missing'], 400);
        }

        $status = $this->cinetPayService->checkPaymentStatus($cpm_trans_id);

        if ($status === 'ACCEPTED') {
            $order = Order::where('stripe_session_id', $cpm_trans_id)->first();
            if ($order) {
                $this->orderService->markOrderAsPaid($order);
                $this->orderService->creditAffiliateCommission($order, null);
            }
        }

        return response()->json(['message' => 'Notified']);
    }

    /**
     * GENIUSPAY
     */
    public function initiateGeniusPay(Request $request, GeniusPayService $geniusPayService)
    {
        $validated = $request->validate([
            'email'            => 'required|email',
            'name'             => 'required|string',
            'phone'            => 'required|string',
            'amount'           => 'required|numeric',
            'items'            => 'required|array',
            'items.*.id'       => 'nullable|integer',
            'items.*.title'    => 'required|string',
            'items.*.price'    => 'required|numeric',
            'affiliate_code'   => 'nullable|string',
        ]);

        $order = $this->orderService->createPendingOrder($validated);
        
        $transactionId = 'GP_' . $order->id . '_' . time();
        $order->update(['stripe_session_id' => $transactionId]);

        $amount = ceil((float)$validated['amount']);

        $paymentData = [
            'amount' => $amount,
            'currency' => 'XOF',
            'customer_email' => $validated['email'],
            'customer_name' => $validated['name'],
            'customer_phone' => $validated['phone'],
            'success_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/order-confirmation?order_id=' . $order->id,
            'error_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/checkout?canceled=true',
            'metadata' => [
                'order_id' => $order->id,
                'transaction_id' => $transactionId
            ],
        ];

        $response = $geniusPayService->initiatePayment($paymentData);

        if ($response['status'] === 'success') {
            return response()->json([
                'payment_url' => $response['paymentUrl'],
                'order_id' => $order->id
            ]);
        }

        return response()->json(['error' => $response['message']], 400);
    }

    public function geniuspayNotify(Request $request)
    {
        $payload = $request->all();
        $payloadContent = $request->getContent();
        
        $signature = $request->header('X-Signature') ?? $request->header('X-GeniusPay-Signature');
        $secret = env('GENIUSPAY_WEBHOOK_SECRET', env('GENIUSPAY_API_SECRET'));

        if ($secret && $signature) {
            $expectedSignature = hash_hmac('sha512', $payloadContent, $secret); 
            $expectedSignature256 = hash_hmac('sha256', $payloadContent, $secret);
            
            if (!hash_equals($expectedSignature, $signature) && !hash_equals($expectedSignature256, $signature)) {
                return response()->json(['error' => 'Signature invalide'], 401);
            }
        }

        if (isset($payload['status']) && $payload['status'] === 'COMPLETED') {
            $transactionId = $payload['metadata']['transaction_id'] ?? null;
            if ($transactionId) {
                $order = Order::where('stripe_session_id', $transactionId)->first();
                if ($order) {
                    $this->orderService->markOrderAsPaid($order);
                    $this->orderService->creditAffiliateCommission($order, null);
                }
            }
        }

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
