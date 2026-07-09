<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        $key = config('services.stripe.secret_key');
        if ($key) {
            Stripe::setApiKey($key);
        }
    }

    public function createPaymentSession(Invoice $invoice, float $amount, int $gatewayId, string $successUrl, string $cancelUrl): ?Session
    {
        try {
            return Session::create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($invoice->currency_code ?? 'usd'),
                        'product_data' => ['name' => 'Invoice #'.$invoice->number],
                        'unit_amount' => (int) round($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'gateway_id' => $gatewayId,
                ],
            ]);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl, ?string $customerId = null): ?Session
    {
        $lineItems = [];

        foreach ($invoice->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($invoice->currency_code ?? 'usd'),
                    'product_data' => ['name' => $item->description],
                    'unit_amount' => (int) round($item->price * 100),
                ],
                'quantity' => $item->quantity ?? 1,
            ];
        }

        $sessionData = [
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ],
        ];

        if ($customerId) {
            $sessionData['customer'] = $customerId;
        }

        try {
            return Session::create($sessionData);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function createCustomer(User $user): ?Customer
    {
        try {
            return Customer::create([
                'email' => $user->email,
                'name' => $user->first_name.' '.$user->last_name,
                'metadata' => ['user_id' => $user->id],
            ]);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function createPaymentIntent(Invoice $invoice, ?string $customerId = null): ?PaymentIntent
    {
        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        try {
            return PaymentIntent::create([
                'amount' => (int) round($totals['total'] * 100),
                'currency' => $invoice->currency_code ?? 'usd',
                'customer' => $customerId,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                ],
            ]);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function getCustomer(string $customerId): ?Customer
    {
        try {
            return Customer::retrieve($customerId);
        } catch (ApiErrorException $e) {
            report($e);

            return null;
        }
    }

    public function constructWebhookEvent(string $payload, string $sigHeader): ?object
    {
        try {
            return Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\UnexpectedValueException|SignatureVerificationException $e) {
            report($e);

            return null;
        }
    }
}
