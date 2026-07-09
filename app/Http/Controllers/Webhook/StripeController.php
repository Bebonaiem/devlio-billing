<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Services\CreditService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function __construct(
        private readonly \App\Services\StripeService $stripe,
        private readonly InvoiceService $invoiceService,
    ) {
        $this->gatewayId = Extension::where('extension', 'stripe')->value('id');
    }

    private ?int $gatewayId = null;

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

        $transaction = InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'gateway_id' => $this->gatewayId,
            'amount' => (float) ($session->amount_total ?? 0) / 100,
            'fee' => 0,
            'transaction_id' => $session->payment_intent ?? $session->id,
            'status' => 'succeeded',
            'is_credit_transaction' => false,
        ]);

        $this->invoiceService->markPaid($invoice, $transaction);
        $this->applyCreditDeposit($invoice);

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

        $transaction = InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'gateway_id' => $this->gatewayId,
            'amount' => (float) ($stripeInvoice->amount_paid ?? 0) / 100,
            'fee' => (float) ($stripeInvoice->charge ?? 0) / 100,
            'transaction_id' => $stripeInvoice->payment_intent ?? $stripeInvoice->id,
            'status' => 'succeeded',
            'is_credit_transaction' => false,
        ]);

        $this->invoiceService->markPaid($invoice, $transaction);
        $this->applyCreditDeposit($invoice);

        return response()->json(['status' => 'success']);
    }

    private function applyCreditDeposit(Invoice $invoice): void
    {
        $item = $invoice->items()->where('reference_id', null)->first();
        if (! $item || ! str_starts_with($item->description, 'Credit Deposit')) {
            return;
        }

        if (preg_match('/\(([A-Z]{3})\s+([\d.]+)\)/', $item->description, $matches)) {
            $currencyCode = $matches[1];
            $amount = (float) $matches[2];
            app(CreditService::class)->add($invoice->user, $amount, $currencyCode);
        }
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
}
