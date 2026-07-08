<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\BillingService;
use App\Services\DiscordService;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(
        private readonly PayPalService $paypal,
        private readonly BillingService $billing,
        private readonly DiscordService $discord,
    ) {}

    public function handleWebhook(Request $request)
    {
        $verified = $this->paypal->verifyWebhook(
            $request->headers->__toString(),
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
            'BILLING.SUBSCRIPTION.ACTIVATED' => $this->handleSubscriptionActivated($event),
            default => response()->json(['status' => 'unhandled']),
        };
    }

    private function handleOrderApproved(array $event)
    {
        $resource = $event['resource'] ?? [];

        // Capture the order
        $result = $this->paypal->captureOrder($resource['id'] ?? '');

        return response()->json(['status' => 'captured']);
    }

    private function handleCaptureCompleted(array $event)
    {
        $resource = $event['resource'] ?? [];

        $invoiceId = null;

        // Try to get invoice ID from custom data
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

        $this->billing->markInvoicePaid(
            $invoice,
            'paypal',
            $transactionId,
            $event
        );

        $this->discord->sendNotification('payment_received', [
            'user' => $invoice->user->name,
            'invoice' => $invoice->invoice_number,
            'amount' => $invoice->total,
        ]);

        return response()->json(['status' => 'success']);
    }

    private function handleSubscriptionActivated(array $event)
    {
        $resource = $event['resource'] ?? [];
        $customId = $resource['custom_id'] ?? null;

        if ($customId) {
            $invoice = Invoice::find($customId);
            if ($invoice && !$invoice->isPaid()) {
                $this->billing->markInvoicePaid(
                    $invoice,
                    'paypal',
                    $resource['id'] ?? 'sub_' . $customId,
                    $event
                );
            }
        }

        return response()->json(['status' => 'success']);
    }
}
