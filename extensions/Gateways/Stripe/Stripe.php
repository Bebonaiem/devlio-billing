<?php

namespace App\Extensions\Gateways\Stripe;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Exceptions\RedirectException;
use App\Helpers\ExtensionHelper;
use App\Models\BillingAgreement;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

#[ExtensionMeta(
    name: 'Stripe',
    description: 'Accept payments via Stripe',
    version: '1.0.0',
    author: 'GameBilling',
    url: 'https://stripe.com'
)]
class Stripe extends Gateway
{
    public function boot(): void
    {
        Route::post('/extensions/stripe/webhook', [$this, 'webhook'])
            ->withoutMiddleware(['verifycsrf'])
            ->name('extensions.stripe.webhook');

        Route::post('/extensions/stripe/charge', [$this, 'chargeAgreement'])
            ->middleware(['web', 'auth'])
            ->name('extensions.stripe.charge');
    }

    public function getConfig(array $values = []): array
    {
        return [
            [
                'name' => 'secret_key',
                'label' => 'Secret Key',
                'type' => 'password',
                'description' => 'Stripe secret API key',
                'required' => true,
                'encrypted' => true,
                'placeholder' => 'sk_live_...',
            ],
            [
                'name' => 'publishable_key',
                'label' => 'Publishable Key',
                'type' => 'text',
                'description' => 'Stripe publishable API key',
                'required' => true,
                'placeholder' => 'pk_live_...',
            ],
            [
                'name' => 'webhook_secret',
                'label' => 'Webhook Signing Secret',
                'type' => 'password',
                'description' => 'Stripe webhook signing secret',
                'required' => true,
                'encrypted' => true,
                'placeholder' => 'whsec_...',
            ],
            [
                'name' => 'test_mode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'description' => 'Use Stripe test mode',
                'default' => false,
            ],
        ];
    }

    public function pay(Invoice $invoice, float $total): void
    {
        $session = $this->createCheckoutSession($invoice, $total);

        $invoice->transactions()->create([
            'gateway_id' => $invoice->gateway_id,
            'amount' => $total,
            'fee' => 0,
            'transaction_id' => $session['id'],
            'status' => 'processing',
        ]);

        throw new RedirectException($session['url']);
    }

    public function supportsBillingAgreements(): bool
    {
        return true;
    }

    public function createBillingAgreement(User $user): string
    {
        $customer = $this->getOrCreateCustomer($user);

        $session = $this->createSetupSession($customer['id'], $user);

        return $session['url'];
    }

    public function cancelBillingAgreement(BillingAgreement $billingAgreement): bool
    {
        $this->apiRequest(
            "/v1/payment_methods/{$billingAgreement->external_reference}",
            'POST',
            ['active' => false]
        );

        return true;
    }

    public function charge(Invoice $invoice, float $total, BillingAgreement $billingAgreement): void
    {
        $this->apiRequest('/v1/payment_intents', 'POST', [
            'amount' => $this->formatAmount($total, $invoice->currency_code),
            'currency' => strtolower($invoice->currency_code),
            'customer' => $billingAgreement->name,
            'payment_method' => $billingAgreement->external_reference,
            'confirm' => true,
            'off_session' => true,
            'description' => "Invoice #{$invoice->number}",
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ],
        ]);
    }

    public function webhook(): Response
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        $event = $this->constructWebhookEvent($payload, $sigHeader);

        if (! $event) {
            return response('Invalid signature', 400);
        }

        match ($event['type']) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event['data']['object']),
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event['data']['object']),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event['data']['object']),
            'setup_intent.succeeded' => $this->handleSetupIntentSucceeded($event['data']['object']),
            default => null,
        };

        return response('OK');
    }

    public function chargeAgreement(): Response
    {
        abort(400, 'This endpoint is called by the billing system.');
    }

    protected function handleCheckoutCompleted(array $session): void
    {
        $invoiceId = $session['metadata']['invoice_id'] ?? null;

        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->isPaid()) {
            return;
        }

        $paymentIntent = $this->apiRequest("/v1/payment_intents/{$session['payment_intent']}", 'GET');

        ExtensionHelper::addPayment(
            $invoice,
            $this->resolveExtensionModel(),
            $paymentIntent['amount_received'] / 100,
            $paymentIntent['charges']['data'][0]['balance_transaction']
                ? $this->getBalanceTransactionFee($paymentIntent['charges']['data'][0]['balance_transaction'])
                : 0,
            $paymentIntent['id'],
            'succeeded'
        );
    }

    protected function handlePaymentSucceeded(array $paymentIntent): void
    {
        $invoiceId = $paymentIntent['metadata']['invoice_id'] ?? null;

        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->isPaid()) {
            return;
        }

        $transaction = $invoice->transactions()
            ->where('transaction_id', $paymentIntent['id'])
            ->first();

        if ($transaction && $transaction->status === 'processing') {
            $transaction->update([
                'status' => 'succeeded',
                'amount' => $paymentIntent['amount_received'] / 100,
            ]);
        }
    }

    protected function handlePaymentFailed(array $paymentIntent): void
    {
        $invoiceId = $paymentIntent['metadata']['invoice_id'] ?? null;

        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return;
        }

        $transaction = $invoice->transactions()
            ->where('transaction_id', $paymentIntent['id'])
            ->first();

        if ($transaction) {
            $transaction->update(['status' => 'failed']);
        }
    }

    protected function handleSetupIntentSucceeded(array $setupIntent): void
    {
        $paymentMethodId = $setupIntent['payment_method'];
        $customerId = $setupIntent['customer'];

        $user = User::where('email', $this->getCustomerEmail($customerId))->first();

        if (! $user) {
            return;
        }

        $paymentMethod = $this->apiRequest("/v1/payment_methods/{$paymentMethodId}", 'GET');

        $user->billingAgreements()->create([
            'gateway_id' => $this->resolveExtensionModel()->id,
            'name' => $customerId,
            'external_reference' => $paymentMethodId,
            'type' => $paymentMethod['card']['brand'] ?? 'card',
        ]);
    }

    protected function createCheckoutSession(Invoice $invoice, float $total): array
    {
        return $this->apiRequest('/v1/checkout/sessions', 'POST', [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $invoice->user->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($invoice->currency_code),
                    'unit_amount' => $this->formatAmount($total, $invoice->currency_code),
                    'product_data' => [
                        'name' => "Invoice #{$invoice->number}",
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ],
        ]);
    }

    protected function createSetupSession(string $customerId, User $user): array
    {
        return $this->apiRequest('/v1/checkout/sessions', 'POST', [
            'payment_method_types' => ['card'],
            'mode' => 'setup',
            'customer' => $customerId,
            'success_url' => route('dashboard.index').'?setup=success',
            'cancel_url' => route('dashboard.index').'?setup=cancelled',
        ]);
    }

    protected function getOrCreateCustomer(User $user): array
    {
        $customers = $this->apiRequest('/v1/customers?email='.urlencode($user->email), 'GET');

        if (! empty($customers['data'])) {
            return $customers['data'][0];
        }

        return $this->apiRequest('/v1/customers', 'POST', [
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);
    }

    protected function constructWebhookEvent(string $payload, string $sigHeader): ?array
    {
        $secret = $this->config('webhook_secret');

        if (! $secret) {
            return null;
        }

        $elements = [];
        foreach (explode(',', $sigHeader) as $pair) {
            [$key, $value] = explode('=', $pair, 2);
            $elements[$key] = $value;
        }

        $signedPayload = $elements['v1'] ?? '';
        $timestamp = $elements['t'] ?? '';

        $signedPayloadContent = "{$timestamp}.{$payload}";
        $expectedSignature = hash_hmac('sha256', $signedPayloadContent, $secret);

        if (! hash_equals($expectedSignature, $signedPayload)) {
            return null;
        }

        return json_decode($payload, true);
    }

    protected function formatAmount(float $amount, string $currency): int
    {
        $zeroDecimal = ['JPY', 'KRW', 'VND', 'CLP', 'ISK', 'UGX', 'RWF', 'GYD', 'CVE', 'BYR'];

        if (in_array(strtoupper($currency), $zeroDecimal)) {
            return (int) $amount;
        }

        return (int) round($amount * 100);
    }

    protected function getBalanceTransactionFee(string $transactionId): float
    {
        try {
            $bt = $this->apiRequest("/v1/balance_transactions/{$transactionId}", 'GET');

            return ($bt['fee'] ?? 0) / 100;
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function getCustomerEmail(string $customerId): string
    {
        $customer = $this->apiRequest("/v1/customers/{$customerId}", 'GET');

        return $customer['email'] ?? '';
    }

    protected function apiRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $secretKey = $this->config('secret_key');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$secretKey,
        ]);

        $response = match ($method) {
            'GET' => $response->get('https://api.stripe.com'.$endpoint),
            'POST' => $response->asForm()->post('https://api.stripe.com'.$endpoint, $data),
            default => $response->get('https://api.stripe.com'.$endpoint),
        };

        if ($response->failed()) {
            throw new \RuntimeException('Stripe API request failed: '.$response->body());
        }

        return $response->json();
    }
}
