<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;
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
                    'currency' => 'usd',
                    'product_data' => ['name' => $item->description],
                    'unit_amount' => (int) ($item->amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $sessionData = [
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ];

        if ($customerId) {
            $sessionData['customer'] = $customerId;
        }

        if ($invoice->order && $invoice->order->plan->billing_cycle !== 'one_time') {
            $sessionData['mode'] = 'subscription';
            $sessionData['line_items'] = [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $invoice->items->first()->description ?? 'Game Server'],
                    'recurring' => [
                        'interval' => match ($invoice->order->plan->billing_cycle) {
                            'monthly' => 'month',
                            'quarterly' => 'month',
                            'semi_annually' => 'month',
                            'annually' => 'year',
                            default => 'month',
                        },
                        'interval_count' => match ($invoice->order->plan->billing_cycle) {
                            'monthly' => 1,
                            'quarterly' => 3,
                            'semi_annually' => 6,
                            'annually' => 1,
                            default => 1,
                        },
                    ],
                    'unit_amount' => (int) ($invoice->total * 100),
                ],
                'quantity' => 1,
            ]];
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
                'name' => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
        } catch (ApiErrorException $e) {
            report($e);
            return null;
        }
    }

    public function createPaymentIntent(Invoice $invoice, ?string $customerId = null): ?PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => (int) ($invoice->total * 100),
                'currency' => 'usd',
                'customer' => $customerId,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
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
