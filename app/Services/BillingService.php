<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\User;

class BillingService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = config('billing.invoice_prefix', 'INV-');
        $nextId = Invoice::max('id') + 1;

        return $prefix.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createInvoice(User $user, array $items, string $currencyCode): Invoice
    {
        return app(InvoiceService::class)->createInvoice($user, $items, $currencyCode);
    }

    public function calculateTotal(Invoice $invoice): array
    {
        return app(InvoiceService::class)->calculateTotal($invoice);
    }

    public function markInvoicePaid(Invoice $invoice, string $gateway, string $gatewayTransactionId, array $gatewayResponse = []): void
    {
        $totals = $this->calculateTotal($invoice);

        $transaction = InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'gateway_id' => null,
            'amount' => $totals['total'],
            'fee' => 0,
            'transaction_id' => $gatewayTransactionId,
            'status' => 'succeeded',
            'is_credit_transaction' => false,
        ]);

        app(InvoiceService::class)->markPaid($invoice, $transaction);
    }

    public function createRenewalInvoice(Service $service): ?Invoice
    {
        return app(InvoiceService::class)->createRenewalInvoice($service);
    }

    public function generateRenewalInvoices(): int
    {
        $count = 0;
        $dueServices = Service::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($dueServices as $service) {
            $hasPending = Invoice::whereHas('items', function ($q) use ($service) {
                $q->where('reference_type', Service::class)
                    ->where('reference_id', $service->id);
            })->where('status', 'pending')
                ->exists();

            if (! $hasPending) {
                $this->createRenewalInvoice($service);
                $count++;
            }
        }

        return $count;
    }

    public function markOverdueInvoices(): int
    {
        $count = Invoice::where('status', 'pending')
            ->where('due_at', '<', now())
            ->update(['status' => 'overdue']);

        return $count;
    }

    public function processOverdueSuspensions(): int
    {
        $graceDays = config('billing.grace_days', 3);
        $cutoff = now()->subDays($graceDays);

        $services = Service::where('status', 'active')
            ->whereHas('invoices', function ($q) use ($cutoff) {
                $q->where('status', 'overdue')
                    ->where('due_at', '<=', $cutoff);
            })
            ->get();

        $count = 0;
        foreach ($services as $service) {
            app(ServiceService::class)->suspendService($service);
            $count++;
        }

        return $count;
    }

    public function processTerminations(): int
    {
        $terminateDays = config('billing.terminate_days', 14);
        $cutoff = now()->subDays($terminateDays);

        $services = Service::where('status', 'suspended')
            ->where('updated_at', '<=', $cutoff)
            ->get();

        $count = 0;
        foreach ($services as $service) {
            app(ServiceService::class)->terminateService($service);
            $count++;
        }

        return $count;
    }
}
