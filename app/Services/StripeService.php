<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl, ?string $customerId = null): ?Session
    {
        $lineItems = [];

        foreach ($invoice->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $invoice->currency_code ?? 'usd',
                    'product_data' => ['name' => $item->description],
                    'unit_amount' => (int) ($item->price * 100),
                ],
                'quantity' => $item->quantity ?? 1,
            ];
        }

        $totals = app(InvoiceService::class)->calculateTotal($invoice);

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
                'name' => $user->first_name . ' ' . $user->last_name,
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
                'amount' => (int) ($totals['total'] * 100),
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
        } catch (\UnexpectedValueException|\Stripe\Exception\SignatureVerificationException $e) {
            report($e);
            return null;
        }
    }
}
