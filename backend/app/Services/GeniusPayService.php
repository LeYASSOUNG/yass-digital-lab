<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniusPayService
{
    protected ?string $apiKey;
    protected ?string $apiSecret;
    protected ?string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.geniuspay.key');
        $this->apiSecret = config('services.geniuspay.secret');
        $this->baseUrl = config('services.geniuspay.url');
    }

    /**
     * Get base HTTP client for GeniusPay.
     */
    protected function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'X-API-Secret' => $this->apiSecret,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Initialize a payment session.
     *
     * @param array $data
     * @return array
     */
    public function initiatePayment(array $data): array
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $endpoint = str_ends_with($baseUrl, '/merchant') ? "{$baseUrl}/payments" : "{$baseUrl}/merchant/payments";

        try {
            $response = $this->client()->post($endpoint, [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'XOF',
                'customer' => [
                    'email' => $data['customer_email'],
                    'name' => $data['customer_name'] ?? 'Client',
                    'phone' => $data['customer_phone'] ?? null,
                ],
                'success_url' => $data['success_url'],
                'error_url' => $data['error_url'],
                'metadata' => $data['metadata'] ?? [],
            ]);

            if ($response->successful()) {
                Log::info('GeniusPay Success Response: ' . $response->body());
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                    'paymentUrl' => $response->json('data.checkout_url') ?? $response->json('checkout_url') ?? $response->json('data.payment_url') ?? $response->json('payment_url') ?? $response->json('data.url') ?? $response->json('url'),
                ];
            }

            Log::error('GeniusPay Error: ' . $response->body());
            
            return [
                'status' => 'error',
                'message' => 'Failed to initialize GeniusPay payment.',
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('GeniusPay Exception: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Exception while connecting to GeniusPay.'
            ];
        }
    }
}
