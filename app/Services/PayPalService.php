<?php
namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private ?string $clientId = null;

    private ?string $clientSecret = null;

    private string $baseUrl = '';

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

        $response = Http::withBasicAuth($this->clientId ?? '', $this->clientSecret ?? '')
            ->asForm()
            ->post($this->baseUrl.'/v1/oauth2/token', [
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
        if (! $token) {
            return ['error' => 'Failed to authenticate with PayPal'];
        }

        $url = $this->baseUrl.'/'.ltrim($endpoint, '/');

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

    public function createOrder(Invoice $invoice, string $returnUrl, string $cancelUrl, ?float $amount = null): ?array
    {
        if ($amount === null) {
            $totals = app(InvoiceService::class)->calculateTotal($invoice);
            $amount = $totals['total'];
        }

        $result = $this->apiRequest('post', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $invoice->id,
                'description' => 'Invoice #'.$invoice->number,
                'amount' => [
                    'currency_code' => $invoice->currency_code ?? 'USD',
                    'value' => number_format($amount, 2, '.', ''),
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
            $approvalUrl = null;
            foreach ($result['links'] ?? [] as $link) {
                if (($link['rel'] ?? '') === 'payer-action') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            return [
                'id' => $result['id'],
                'approval_url' => $approvalUrl,
            ];
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

    public function verifyWebhookHeaders(array $headers, string $body): bool
    {
        $webhookId = config('services.paypal.webhook_id');
        if (! $webhookId) {
            return false;
        }

        $authAlgo = $headers['paypal-auth-algo'][0] ?? null;
        $certUrl = $headers['paypal-cert-url'][0] ?? null;
        $transmissionId = $headers['paypal-transmission-id'][0] ?? null;
        $transmissionSig = $headers['paypal-transmission-sig'][0] ?? null;
        $transmissionTime = $headers['paypal-transmission-time'][0] ?? null;

        if (! $authAlgo || ! $certUrl || ! $transmissionId || ! $transmissionSig || ! $transmissionTime) {
            return false;
        }

        $result = $this->apiRequest('post', '/v1/notifications/verify-webhook-signature', [
            'auth_algo' => $authAlgo,
            'cert_url' => $certUrl,
            'transmission_id' => $transmissionId,
            'transmission_sig' => $transmissionSig,
            'transmission_time' => $transmissionTime,
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($body, true),
        ]);

        return ($result['verification_status'] ?? '') === 'SUCCESS';
    }
}
