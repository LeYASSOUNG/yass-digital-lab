<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CinetPayService
{
    protected $apiKey;
    protected $siteId;
    protected $notifyUrl;

    public function __construct()
    {
        $this->apiKey = config('cinetpay.api_key');
        $this->siteId = config('cinetpay.site_id');
        $this->notifyUrl = config('cinetpay.notify_url');
    }

    public function initiatePayment(Order $order, string $email): array
    {
        $transactionId = 'YASS_' . $order->id . '_' . time();
        $order->update(['stripe_session_id' => $transactionId]); // On réutilise stripe_session_id pour le cpm_trans_id
        
        $amount = ceil((float)$order->total_amount);

        $payload = [
            'apikey'       => $this->apiKey,
            'site_id'      => $this->siteId,
            'transaction_id' => $transactionId,
            'amount'       => $amount,
            'currency'     => 'XOF',
            'description'  => 'Commande Yass Digital Lab',
            'customer_name'=> 'Client',
            'customer_surname'=> 'Yass',
            'customer_email'=> $email,
            'customer_phone_number' => '00000000',
            'customer_address' => 'BP 000',
            'customer_city'=> 'Abidjan',
            'customer_country'=> 'CI',
            'customer_state'=> 'CI',
            'customer_zip_code'=> '00000',
            'notify_url'   => $this->notifyUrl,
            'return_url'   => env('FRONTEND_URL', 'http://localhost:5173') . '/order-confirmation?order_id=' . $order->id,
            'channels'     => 'ALL',
            'lang'         => 'fr',
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api-checkout.cinetpay.com/v2/payment', [
            'json' => $payload
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if ($result['code'] == '201') {
            return [
                'success' => true,
                'payment_url' => $result['data']['payment_url'],
                'order_id' => $order->id
            ];
        }

        return [
            'success' => false,
            'message' => 'Erreur CinetPay : ' . $result['message']
        ];
    }

    public function checkPaymentStatus(string $transactionId): ?string
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api-checkout.cinetpay.com/v2/payment/check', [
            'json' => [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if ($result['code'] == '00') {
            return $result['data']['status']; // ex: 'ACCEPTED'
        }

        return null;
    }
}
