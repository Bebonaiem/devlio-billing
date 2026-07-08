<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceSnapshot;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\User;
use App\Jobs\ProvisionServer;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private readonly TaxService $tax,
        private readonly CurrencyService $currency,
    ) {}

    public function createInvoice(User $user, array $items, string $currencyCode): Invoice
    {
        return DB::transaction(function () use ($user, $items, $currencyCode) {
            $invoice = Invoice::create([
                'number' => $this->generateNumber(),
                'user_id' => $user->id,
                'currency_code' => $currencyCode,
                'status' => 'pending',
                'due_at' => now()->addDays(config('billing.invoice_due_days', 7)),
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'description' => $item['description'] ?? '',
                    'reference_id' => $item['reference_id'] ?? null,
                    'reference_type' => $item['reference_type'] ?? null,
                ]);
            }

            return $invoice;
        });
    }

    public function calculateTotal(Invoice $invoice): array
    {
        $subtotal = $invoice->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $country = null;
        if ($invoice->user) {
            $country = $this->tax->getUserCountry($invoice->user);
        }

        $taxResult = $this->tax->calculate($subtotal, $country ?? 'all');

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => $taxResult['tax_amount'],
            'tax_rate' => $taxResult['tax_rate'],
            'tax_name' => $taxResult['tax_name'],
            'total' => round($subtotal + $taxResult['tax_amount'], 2),
        ];
    }

    public function markPaid(Invoice $invoice, InvoiceTransaction $transaction): void
    {
        DB::transaction(function () use ($invoice, $transaction) {
            $invoice->update(['status' => 'paid']);

            $this->createSnapshot($invoice);

            $invoiceItems = $invoice->items()->whereNotNull('reference_id')->get();

            foreach ($invoiceItems as $item) {
                if ($item->reference_type === Service::class) {
                    $service = Service::find($item->reference_id);

                    if ($service) {
                        if ($service->status === 'pending') {
                            $service->update(['status' => 'active']);
                            $this->dispatchProvisioning($service);
                        } elseif ($service->status === 'suspended') {
                            $service->update(['status' => 'active']);
                            $this->dispatchUnsuspend($service);
                        }
                    }
                }
            }
        });
    }

    public function generateNumber(): string
    {
        $prefix = config('billing.invoice_prefix', 'INV-');
        $nextId = Invoice::max('id') + 1;

        return $prefix . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createRenewalInvoice(Service $service): ?Invoice
    {
        $user = $service->user;
        if (! $user) {
            return null;
        }

        $product = $service->product;
        $plan = $service->plan;

        $description = $product->name ?? 'Service';
        if ($plan) {
            $description .= ' - ' . $plan->name;
        }

        return $this->createInvoice($user, [
            [
                'quantity' => 1,
                'price' => (float) $service->price,
                'description' => $description,
                'reference_id' => $service->id,
                'reference_type' => Service::class,
            ],
        ], $service->currency_code);
    }

    public function markCancelled(Invoice $invoice): void
    {
        $invoice->update(['status' => 'cancelled']);
    }

    private function createSnapshot(Invoice $invoice): void
    {
        $totals = $this->calculateTotal($invoice);
        $user = $invoice->user;

        InvoiceSnapshot::create([
            'invoice_id' => $invoice->id,
            'name' => $invoice->number,
            'properties' => [
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'items' => $invoice->items->map(fn ($item) => [
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ])->toArray(),
            ],
            'tax_name' => $totals['tax_name'],
            'tax_rate' => $totals['tax_rate'],
            'tax_country' => $user ? $this->tax->getUserCountry($user) : null,
            'bill_to' => $user?->name . ' <' . $user?->email . '>',
        ]);
    }

    private function dispatchProvisioning(Service $service): void
    {
        try {
            $job = new ProvisionServer($service);
            dispatch($job);
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function dispatchUnsuspend(Service $service): void
    {
        try {
            app(\App\Services\ServiceService::class)->unsuspend($service);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
