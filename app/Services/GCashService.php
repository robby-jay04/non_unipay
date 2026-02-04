<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GCashService
{
    protected $apiUrl;
    protected $apiKey;
    protected $merchantId;

    public function __construct()
    {
        $this->apiUrl = config('services.gcash.api_url');
        $this->apiKey = config('services.gcash.api_key');
        $this->merchantId = config('services.gcash.merchant_id');
    }

    public function createPayment($amount, $referenceNo)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/payments', [
                'merchant_id' => $this->merchantId,
                'amount' => $amount,
                'reference_no' => $referenceNo,
                'redirect_url' => route('payment.callback'),
                'callback_url' => route('api.payment.webhook'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GCash payment creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('GCash API error: ' . $response->status());
        } catch (\Exception $e) {
            Log::error('GCash service error', [
                'message' => $e->getMessage(),
                'reference_no' => $referenceNo,
            ]);

            // For development/testing - return mock response
            if (config('app.env') === 'local') {
                return [
                    'payment_url' => route('payment.mock', ['reference' => $referenceNo]),
                    'transaction_id' => 'MOCK-' . $referenceNo,
                    'status' => 'pending',
                ];
            }

            throw $e;
        }
    }

    public function verifyPayment($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/payments/' . $transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Payment verification failed');
        } catch (\Exception $e) {
            Log::error('GCash verification error', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            // Mock response for development
            if (config('app.env') === 'local') {
                return [
                    'transaction_id' => $transactionId,
                    'status' => 'success',
                    'reference_no' => str_replace('MOCK-', '', $transactionId),
                ];
            }

            throw $e;
        }
    }

    public function refundPayment($transactionId, $amount)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/refunds', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('GCash refund error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
