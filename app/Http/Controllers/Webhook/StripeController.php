<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\DiscordService;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function __construct(
        private readonly StripeService $stripe,
        private readonly BillingService $billing,
        private readonly DiscordService $discord,
    ) {}

    public function handleWebhook(Request $request)
    {
        $event = $this->stripe->constructWebhookEvent(
            $request->getContent(),
            $request->header('Stripe-Signature')
        );

        if (!$event) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'invoice.paid' => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            default => response()->json(['status' => 'unhandled']),
        };
    }

    private function handleCheckoutCompleted(object $session)
    {
        $invoiceId = $session->metadata->invoice_id ?? null;
        if (!$invoiceId) {
            return response()->json(['error' => 'No invoice ID'], 400);
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice || $invoice->isPaid()) {
            return response()->json(['status' => 'already_paid']);
        }

        $this->billing->markInvoicePaid(
            $invoice,
            'stripe',
            $session->payment_intent ?? $session->id,
            json_decode(json_encode($session), true)
        );

        $this->discord->sendNotification('payment_received', [
            'user' => $invoice->user->name,
            'invoice' => $invoice->invoice_number,
            'amount' => $invoice->total,
        ]);

        return response()->json(['status' => 'success']);
    }

    private function handleInvoicePaid(object $stripeInvoice)
    {
        $invoiceId = $stripeInvoice->metadata->invoice_id ?? null;
        if (!$invoiceId) {
            return response()->json(['error' => 'No invoice ID'], 400);
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice || $invoice->isPaid()) {
            return response()->json(['status' => 'already_paid']);
        }

        $this->billing->markInvoicePaid(
            $invoice,
            'stripe',
            $stripeInvoice->payment_intent ?? $stripeInvoice->id,
            json_decode(json_encode($stripeInvoice), true)
        );

        return response()->json(['status' => 'success']);
    }

    private function handleInvoicePaymentFailed(object $stripeInvoice)
    {
        $invoiceId = $stripeInvoice->metadata->invoice_id ?? null;
        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoice->update(['status' => 'overdue']);
            }
        }

        return response()->json(['status' => 'logged']);
    }

    private function handleSubscriptionUpdated(object $subscription)
    {
        // Handle subscription status changes
        return response()->json(['status' => 'handled']);
    }
}
