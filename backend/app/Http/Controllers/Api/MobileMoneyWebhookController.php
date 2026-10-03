<?php

/**
 * ============================================================
 * MobileMoneyWebhookController — Yass Digital Lab
 * ============================================================
 * Contrôleur de traitement des Webhooks Mobile Money en temps réel.
 * Intercepte et valide les callbacks de paiement de Wave, Orange Money et MTN MoMo.
 * ============================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Commission;
use App\Models\User;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MobileMoneyWebhookController extends Controller
{
    /**
     * Traitement du Webhook Wave (wave.com).
     * Vérification de la signature HMAC 'X-Wave-Signature'.
     */
    public function handleWave(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Wave-Signature');
        $webhookSecret = env('WAVE_WEBHOOK_SECRET');

        if ($webhookSecret && $signature) {
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Signature Webhook Wave invalide');
                return response()->json(['message' => 'Signature Webhook Wave invalide'], 401);
            }
        }

        $data = $request->all();
        $reference = $data['data']['client_reference'] ?? $data['client_reference'] ?? null;
        $orderId = $data['data']['metadata']['order_id'] ?? null;

        return $this->processSuccessPayment('wave', $orderId, $reference);
    }

    /**
     * Traitement du Webhook Orange Money (Orange API).
     */
    public function handleOrangeMoney(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $secretToken = env('ORANGE_MONEY_WEBHOOK_TOKEN');

        if ($secretToken && $authHeader !== 'Bearer ' . $secretToken) {
            Log::warning('Jeton Webhook Orange Money invalide');
            return response()->json(['message' => 'Token d\'autorisation invalide'], 401);
        }

        $data = $request->all();
        $reference = $data['notif_token'] ?? $data['order_id'] ?? null;

        return $this->processSuccessPayment('orange_money', null, $reference);
    }

    /**
     * Traitement du Callback MTN Mobile Money (MTN MoMo API).
     */
    public function handleMtnMoMo(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $secretToken = env('MTN_MOMO_WEBHOOK_TOKEN');

        if ($secretToken && $authHeader !== 'Bearer ' . $secretToken) {
            Log::warning('Jeton Webhook MTN MoMo invalide');
            return response()->json(['message' => 'Token d\'autorisation invalide'], 401);
        }

        $data = $request->all();
        $status = $data['status'] ?? 'SUCCESSFUL';
        $reference = $data['externalId'] ?? null;

        if (strtoupper($status) !== 'SUCCESSFUL') {
            return response()->json(['message' => 'Paiement MTN non complété'], 200);
        }

        return $this->processSuccessPayment('mtn', null, $reference);
    }

    /**
     * Traitement du Callback Moov Money (Moov Africa API / Flooz).
     */
    public function handleMoovMoney(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $secretToken = env('MOOV_MONEY_WEBHOOK_TOKEN');

        if ($secretToken && $authHeader !== 'Bearer ' . $secretToken) {
            Log::warning('Jeton Webhook Moov Money invalide');
            return response()->json(['message' => 'Token d\'autorisation invalide'], 401);
        }

        $data = $request->all();
        $status = $data['status'] ?? $data['result_code'] ?? '0';
        $reference = $data['reference'] ?? $data['trans_id'] ?? null;

        if ($status !== '0' && strtoupper($status) !== 'SUCCESS' && strtoupper($status) !== 'SUCCESSFUL') {
            return response()->json(['message' => 'Paiement Moov Money non validé'], 200);
        }

        return $this->processSuccessPayment('moov', null, $reference);
    }

    /**
     * Endpoint de simulation de Webhook Mobile Money (pour tests dev / démo en local).
     */
    public function simulateWebhook(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'provider' => 'nullable|string',
        ]);

        $orderId = $request->input('order_id');
        $provider = $request->input('provider', 'wave');

        return $this->processSuccessPayment($provider, $orderId, null);
    }

    /**
     * Traitement centralisé de validation du paiement Mobile Money.
     */
    private function processSuccessPayment(string $provider, ?int $orderId, ?string $reference)
    {
        $order = null;

        if ($orderId) {
            $order = Order::find($orderId);
        } elseif ($reference) {
            $order = Order::where('stripe_session_id', $reference)
                ->orWhere('id', str_replace('MOMO-', '', $reference))
                ->first();
        }

        if (!$order) {
            Log::warning("Webhook reçu pour la référence {$reference} mais aucune commande ne correspond.");
            return response()->json(['message' => 'Aucune commande correspondante trouvée.'], 404);
        }

        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);

            // 1. Envoi e-mail de confirmation avec lien de facture signé
            $this->sendConfirmationEmail($order);

            // 2. Attribution automatique de commission d'affiliation
            $this->awardAffiliateCommission($order);

            Log::info("Commande #{$order->id} validée en temps réel via Webhook Mobile Money ({$provider}) !");
        }

        return response()->json([
            'message'  => "Paiement Mobile Money {$provider} validé avec succès pour la commande #{$order->id}.",
            'order_id' => $order->id,
            'status'   => 'paid',
        ], 200);
    }

    private function sendConfirmationEmail(Order $order): void
    {
        try {
            $signedInvoiceUrl = URL::temporarySignedRoute(
                'orders.invoice',
                now()->addDays(30),
                ['id' => $order->id]
            );
            Mail::to($order->email)->send(new OrderConfirmation($order->load('items'), $signedInvoiceUrl));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email Webhook Mobile Money : ' . $e->getMessage());
        }
    }

    private function awardAffiliateCommission(Order $order): void
    {
        $affiliateCode = request()->input('affiliate_code');
        if (empty($affiliateCode)) return;

        $affiliate = User::where('affiliate_code', trim($affiliateCode))->first();
        if (!$affiliate || strtolower($affiliate->email) === strtolower($order->email)) return;

        if (Commission::where('order_id', $order->id)->exists()) return;

        $commissionAmount = round(((float) $order->total_amount) * 0.15, 2);
        if ($commissionAmount <= 0) return;

        Commission::create([
            'affiliate_id'      => $affiliate->id,
            'order_id'          => $order->id,
            'affiliate_code'    => $affiliate->affiliate_code,
            'order_amount'      => (float) $order->total_amount,
            'commission_rate'   => 15.00,
            'commission_amount' => $commissionAmount,
            'status'            => 'approved',
        ]);

        $affiliate->affiliate_balance += $commissionAmount;
        $affiliate->save();
    }
}
