<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            $this->accessToken = $response->json('access_token');
            return $this->accessToken;
        }

        Log::error('Failed to get PayPal access token', ['response' => $response->body()]);
        return null;
    }

    private function apiRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => 'Failed to authenticate with PayPal'];
        }

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $response = Http::withToken($token)
            ->withHeader('Content-Type', 'application/json')
            ->$method($url, $method === 'get' ? [] : $data);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('PayPal API request failed', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return ['error' => $response->body()];
    }

    public function createOrder(Invoice $invoice, string $returnUrl, string $cancelUrl): ?string
    {
        $result = $this->apiRequest('post', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $invoice->id,
                'description' => 'Invoice #' . $invoice->invoice_number,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($invoice->total, 2, '.', ''),
                ],
            ]],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ],
            ],
        ]);

        if (isset($result['id'])) {
            return $result['id'];
        }

        return null;
    }

    public function captureOrder(string $orderId): array
    {
        return $this->apiRequest('post', "/v2/checkout/orders/{$orderId}/capture");
    }

    public function getOrder(string $orderId): array
    {
        return $this->apiRequest('get', "/v2/checkout/orders/{$orderId}");
    }

    public function createSubscription(Invoice $invoice, string $returnUrl, string $cancelUrl): ?string
    {
        $billingCycle = $invoice->order?->plan?->billing_cycle ?? 'monthly';

        $intervalMap = [
            'monthly' => ['unit' => 'MONTH', 'count' => 1],
            'quarterly' => ['unit' => 'MONTH', 'count' => 3],
            'semi_annually' => ['unit' => 'MONTH', 'count' => 6],
            'annually' => ['unit' => 'YEAR', 'count' => 1],
        ];

        $interval = $intervalMap[$billingCycle] ?? ['unit' => 'MONTH', 'count' => 1];

        $result = $this->apiRequest('post', '/v1/billing/subscriptions', [
            'plan' => [
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee' => [
                        'value' => '0',
                        'currency_code' => 'USD',
                    ],
                ],
            ],
            'custom_id' => (string) $invoice->id,
            'subscriber' => [
                'name' => ['given_name' => $invoice->user->name],
                'email_address' => $invoice->user->email,
            ],
            'application_context' => [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ]);

        if (isset($result['id'])) {
            return $result['id'];
        }

        return null;
    }

    public function verifyWebhook(string $headers, string $body): bool
    {
        $webhookId = config('services.paypal.webhook_id');

        $result = $this->apiRequest('post', '/v1/notifications/verify-webhook-signature', [
            'auth_algo' => $this->extractHeader($headers, 'PAYPAL-AUTH-ALGO'),
            'cert_url' => $this->extractHeader($headers, 'PAYPAL-CERT-URL'),
            'transmission_id' => $this->extractHeader($headers, 'PAYPAL-TRANSMISSION-ID'),
            'transmission_sig' => $this->extractHeader($headers, 'PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $this->extractHeader($headers, 'PAYPAL-TRANSMISSION-TIME'),
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($body, true),
        ]);

        return ($result['verification_status'] ?? '') === 'SUCCESS';
    }

    private function extractHeader(string $headers, string $name): ?string
    {
        foreach (explode("\n", $headers) as $line) {
            if (str_starts_with(strtolower($line), strtolower($name))) {
                return trim(substr($line, strpos($line, ':') + 1));
            }
        }
        return null;
    }
}
