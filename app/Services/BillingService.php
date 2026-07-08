<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;

class BillingService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = config('billing.invoice_prefix', 'INV-');
        $nextId = Invoice::max('id') + 1;
        return $prefix . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createInvoice(Order $order, ?string $status = 'pending'): Invoice
    {
        $plan = $order->plan;
        $dueDate = $order->next_due_date ?? now();

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'subtotal' => $plan->price,
            'tax' => 0,
            'total' => $plan->price,
            'status' => $status,
            'due_date' => $dueDate,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => "{$plan->product->name} - {$plan->name} ({$plan->billing_label})",
            'amount' => $plan->price,
        ]);

        return $invoice;
    }

    public function createInitialInvoice(Order $order): Invoice
    {
        $plan = $order->plan;
        $total = $plan->price + $plan->setup_fee;

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'subtotal' => $total,
            'tax' => 0,
            'total' => $total,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        if ($plan->setup_fee > 0) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Setup Fee - {$plan->product->name} ({$plan->name})",
                'amount' => $plan->setup_fee,
            ]);
        }

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => "{$plan->product->name} - {$plan->name} ({$plan->billing_label})",
            'amount' => $plan->price,
        ]);

        return $invoice;
    }

    public function markInvoicePaid(Invoice $invoice, string $gateway, string $gatewayTransactionId, array $gatewayResponse = []): void
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Transaction::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'gateway' => $gateway,
            'gateway_transaction_id' => $gatewayTransactionId,
            'amount' => $invoice->total,
            'currency' => 'USD',
            'status' => 'completed',
            'gateway_response' => $gatewayResponse,
        ]);

        if ($invoice->order) {
            $this->activateOrder($invoice->order);
        }
    }

    public function activateOrder(Order $order): void
    {
        $plan = $order->plan;
        $nextDueDate = match ($plan->billing_cycle) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'semi_annually' => now()->addMonths(6),
            'annually' => now()->addYear(),
            default => now()->addMonth(),
        };

        $order->update([
            'status' => 'active',
            'next_due_date' => $nextDueDate,
            'expires_at' => $nextDueDate,
        ]);
    }

    public function generateRenewalInvoices(): int
    {
        $count = 0;
        $dueOrders = Order::where('status', 'active')
            ->where('next_due_date', '<=', now())
            ->get();

        foreach ($dueOrders as $order) {
            // Skip if there's already a pending invoice
            $hasPending = Invoice::where('order_id', $order->id)
                ->where('status', 'pending')
                ->exists();

            if (!$hasPending) {
                $this->createInvoice($order);
                $count++;
            }
        }

        return $count;
    }

    public function markOverdueInvoices(): int
    {
        $count = Invoice::where('status', 'pending')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return $count;
    }

    public function processOverdueSuspensions(int $graceDays = 3): int
    {
        $count = 0;
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->where('due_date', '<', now()->subDays($graceDays))
            ->whereNotNull('order_id')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $order = $invoice->order;
            if ($order && $order->status === 'active') {
                $order->update(['status' => 'suspended']);
                if ($order->server) {
                    $order->server->update(['status' => 'suspended']);
                }
                $count++;
            }
        }

        return $count;
    }

    public function processTerminations(int $terminateDays = 14): int
    {
        $count = 0;
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->where('due_date', '<', now()->subDays($terminateDays))
            ->whereNotNull('order_id')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $order = $invoice->order;
            if ($order && $order->status !== 'terminated') {
                $order->update(['status' => 'terminated']);
                if ($order->server) {
                    $order->server->update(['status' => 'terminated']);
                }
                $count++;
            }
        }

        return $count;
    }

    public function addCredits(User $user, float $amount, ?string $description = null): Transaction
    {
        $user->increment('credit_balance', $amount);

        return Transaction::create([
            'user_id' => $user->id,
            'gateway' => 'credit',
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
            'gateway_response' => ['description' => $description ?? 'Credit added'],
        ]);
    }

    public function deductCredits(User $user, float $amount, ?Invoice $invoice = null): bool
    {
        if ($user->credit_balance < $amount) {
            return false;
        }

        $user->decrement('credit_balance', $amount);

        Transaction::create([
            'invoice_id' => $invoice?->id,
            'user_id' => $user->id,
            'gateway' => 'credit',
            'amount' => -$amount,
            'currency' => 'USD',
            'status' => 'completed',
            'gateway_response' => ['description' => 'Credit used for invoice ' . ($invoice?->invoice_number ?? 'N/A')],
        ]);

        return true;
    }
}
