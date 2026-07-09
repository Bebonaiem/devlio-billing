<?php

namespace App\Extensions\Gateways\PayPal;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Exceptions\RedirectException;
use App\Models\BillingAgreement;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

#[ExtensionMeta(
    name: 'PayPal',
    description: 'Accept payments via PayPal',
    version: '1.0.0',
    author: 'GameBilling',
    url: 'https://paypal.com'
)]
class PayPal extends Gateway
{
    protected ?string $accessToken = null;

    public function boot(): void
    {
        Route::post('/extensions/paypal/webhook', [$this, 'webhook'])
            ->withoutMiddleware(['verifycsrf'])
            ->name('extensions.paypal.webhook');
    }

    public function getConfig(array $values = []): array
    {
        return [
            [
                'name' => 'client_id',
                'label' => 'Client ID',
                'type' => 'text',
                'description' => 'PayPal API Client ID',
                'required' => true,
                'placeholder' => 'AY...',
            ],
            [
                'name' => 'secret',
                'label' => 'Secret',
                'type' => 'password',
                'description' => 'PayPal API Secret',
                'required' => true,
                'encrypted' => true,
            ],
            [
                'name' => 'webhook_id',
                'label' => 'Webhook ID',
                'type' => 'text',
                'description' => 'PayPal Webhook ID',
                'required' => true,
                'placeholder' => '4TU...]',
            ],
            [
                'name' => 'sandbox',
                'label' => 'Sandbox Mode',
                'type' => 'checkbox',
                'description' => 'Use PayPal sandbox for testing',
                'default' => false,
            ],
        ];
    }

    public function pay(Invoice $invoice, float $total): void
    {
        $order = $this->createOrder($invoice, $total);

        $invoice->transactions()->create([
            'gateway_id' => $invoice->gateway_id,
            'amount' => $total,
            'fee' => 0,
            'transaction_id' => $order['id'],
            'status' => 'processing',
        ]);

        $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

        if ($approveUrl) {
            throw new RedirectException($approveUrl);
        }
    }

    public function supportsBillingAgreements(): bool
    {
        return true;
    }

    public function createBillingAgreement(User $user): string
    {
        $token = $this->createVaultToken();
        $approveUrl = collect($token['links'])->firstWhere('rel', 'approve')['href'] ?? null;

        return $approveUrl ?? '';
    }

    public function cancelBillingAgreement(BillingAgreement $billingAgreement): bool
    {
        $this->paypalApi("/v1/vault/payment-tokens/{$billingAgreement->external_reference}", 'DELETE');

        return true;
    }

    public function charge(Invoice $invoice, float $total, BillingAgreement $billingAgreement): void
    {
        $response = $this->paypalApi('/v1/vault/payment-tokens/'.$billingAgreement->external_reference.'/reauthorize', 'POST', [
            'amount' => [
                'currency_code' => $invoice->currency_code,
                'value' => number_format($total, 2, '.', ''),
            ],
        ]);

        $invoice->transactions()->create([
            'gateway_id' => $invoice->gateway_id,
            'amount' => $total,
            'fee' => 0,
            'transaction_id' => $response['id'] ?? uniqid('pp_'),
            'status' => 'succeeded',
        ]);
    }

    public function webhook(): Response
    {
        $payload = file_get_contents('php://input');
        $headers = getallheaders();

        if (! $this->verifyWebhookHeaders($headers, $payload)) {
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);

        match ($event['event_type'] ?? '') {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($event['resource'] ?? []),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($event['resource'] ?? []),
            default => null,
        };

        return response('OK');
    }

    protected function handleOrderApproved(array $order): void
    {
        $invoiceId = $order['purchase_units'][0]['custom_id'] ?? null;

        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->isPaid()) {
            return;
        }

        $captureUrl = "/v2/checkout/orders/{$order['id']}/capture";
        $response = $this->paypalApi($captureUrl, 'POST');

        $capture = $response['purchase_units'][0]['payments']['captures'][0] ?? null;

        if ($capture && $capture['status'] === 'COMPLETED') {
            $transaction = $invoice->transactions()
                ->where('transaction_id', $order['id'])
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'succeeded',
                    'amount' => (float) $capture['amount']['value'],
                ]);
            }
        }
    }

    protected function handleCaptureCompleted(array $capture): void
    {
        $customId = $capture['custom_id'] ?? null;

        if (! $customId) {
            return;
        }

        $invoice = Invoice::find($customId);

        if ($invoice && ! $invoice->isPaid()) {
            $transaction = $invoice->transactions()
                ->where('transaction_id', $capture['id'])
                ->first();

            if ($transaction && $transaction->status === 'processing') {
                $transaction->update([
                    'status' => 'succeeded',
                    'amount' => (float) $capture['amount']['value'],
                ]);
            }
        }
    }

    protected function createOrder(Invoice $invoice, float $total): array
    {
        return $this->paypalApi('/v2/checkout/orders', 'POST', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'INV-'.$invoice->number,
                'custom_id' => (string) $invoice->id,
                'amount' => [
                    'currency_code' => $invoice->currency_code,
                    'value' => number_format($total, 2, '.', ''),
                ],
                'description' => "Invoice #{$invoice->number}",
            ]],
            'application_context' => [
                'return_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
                'brand_name' => config('app.name'),
            ],
        ]);
    }

    protected function createVaultToken(): array
    {
        return $this->paypalApi('/v1/vault/payment-tokens', 'POST', [
            'payment_source' => [
                'paypal' => [
                    'usage_type' => 'MERCHANT',
                    'customer_type' => 'CONSUMER',
                ],
            ],
        ]);
    }

    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::withBasicAuth(
            $this->config('client_id'),
            $this->config('secret')
        )->post($this->getBaseUrl().'/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('PayPal authentication failed');
        }

        $this->accessToken = $response->json()['access_token'];

        return $this->accessToken;
    }

    protected function paypalApi(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $url = $this->getBaseUrl().$endpoint;

        $response = Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Prefer' => 'return=representation',
            ]);

        $response = match ($method) {
            'GET' => $response->get($url),
            'POST' => $response->post($url, $data),
            'DELETE' => $response->delete($url),
            default => $response->get($url),
        };

        if ($response->failed() && $response->status() !== 204) {
            throw new \RuntimeException('PayPal API request failed: '.$response->body());
        }

        return $response->json() ?? [];
    }

    protected function verifyWebhookHeaders(array $headers, string $payload): bool
    {
        $webhookId = $this->config('webhook_id');

        $transmissionId = $headers['PayPal-Transmission-Id'] ?? '';
        $timestamp = $headers['PayPal-Transmission-Time'] ?? '';
        $webhookEvent = $headers['PayPal-Webhook-Id'] ?? '';
        $certUrl = $headers['PayPal-Cert-Url'] ?? '';
        $actualSig = $headers['PayPal-Trans-Sig'] ?? '';

        if ($webhookEvent !== $webhookId) {
            return false;
        }

        $expectedSig = hash_hmac('sha256', $transmissionId.$timestamp.$webhookId.$payload, $this->config('secret'));

        return hash_equals($expectedSig, $actualSig);
    }

    protected function getBaseUrl(): string
    {
        return $this->config('sandbox')
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }
}
