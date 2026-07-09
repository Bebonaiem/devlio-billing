<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Services\CreditService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(
        private readonly \App\Services\PayPalService $paypal,
        private readonly InvoiceService $invoiceService,
    ) {
        $this->gatewayId = Extension::where('extension', 'paypal')->value('id');
    }

    private ?int $gatewayId = null;

    public function handleWebhook(Request $request)
    {
        $verified = $this->paypal->verifyWebhookHeaders(
            $request->header(),
            $request->getContent()
        );

        if (!$verified) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->all();
        $eventType = $event['event_type'] ?? '';

        return match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($event),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($event),
            default => response()->json(['status' => 'unhandled']),
        };
    }

    private function handleOrderApproved(array $event)
    {
        $resource = $event['resource'] ?? [];
        $this->paypal->captureOrder($resource['id'] ?? '');

        return response()->json(['status' => 'captured']);
    }

    private function handleCaptureCompleted(array $event)
    {
        $resource = $event['resource'] ?? [];

        $invoiceId = null;
        $purchaseUnits = $resource['purchase_units'] ?? [];
        foreach ($purchaseUnits as $unit) {
            $invoiceId = $unit['reference_id'] ?? null;
            if ($invoiceId) break;
        }

        if (!$invoiceId) {
            return response()->json(['error' => 'No invoice reference'], 400);
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice || $invoice->isPaid()) {
            return response()->json(['status' => 'already_paid']);
        }

        $transactionId = $resource['id'] ?? $event['id'] ?? 'unknown';

        $amount = 0;
        foreach ($purchaseUnits as $unit) {
            if (isset($unit['amount']['value'])) {
                $amount = (float) $unit['amount']['value'];
                break;
            }
        }

        $transaction = InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'gateway_id' => $this->gatewayId,
            'amount' => $amount,
            'fee' => 0,
            'transaction_id' => $transactionId,
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
}
