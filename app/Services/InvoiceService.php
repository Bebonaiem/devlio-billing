<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceSnapshot;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private readonly TaxService $tax,
        private readonly CurrencyService $currency,
    ) {}

    public function createInvoice(User $user, array $items, string $currencyCode, ?int $orderId = null): Invoice
    {
        return DB::transaction(function () use ($user, $items, $currencyCode, $orderId) {
            $invoice = Invoice::create([
                'number' => $this->generateNumber(),
                'user_id' => $user->id,
                'order_id' => $orderId,
                'currency_code' => $currencyCode,
                'status' => Invoice::STATUS_PENDING,
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
                    'gateway_id' => $item['gateway_id'] ?? null,
                ]);
            }

            return $invoice;
        });
    }

    public function calculateTotal(Invoice $invoice): array
    {
        $subtotal = (float) $invoice->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $taxResult = $this->tax->calculateForUser($subtotal, $invoice->user);

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => $taxResult['tax_amount'] ?? 0,
            'tax_rate' => $taxResult['tax_rate'] ?? 0,
            'tax_name' => $taxResult['tax_name'] ?? '',
            'total' => round($subtotal + ($taxResult['tax_amount'] ?? 0), 2),
        ];
    }

    public function markPaid(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => Invoice::STATUS_PAID]);

            $this->createSnapshot($invoice);

            app(ProcessPaidInvoiceService::class)->handle($invoice);
        });
    }

    public function generateNumber(): string
    {
        return DB::transaction(function () {
            $setting = Setting::where('key', 'invoice_number')->lockForUpdate()->first();

            if (! $setting) {
                $setting = Setting::create(['key' => 'invoice_number', 'value' => '0']);
            }

            $number = (int) $setting->value + 1;
            $setting->update(['value' => (string) $number]);

            $prefix = config('settings.invoice_prefix', 'INV-');
            $format = config('settings.invoice_format', '{number}');

            $formatted = str_replace('{number}', str_pad((string) $number, 6, '0', STR_PAD_LEFT), $format);
            $formatted = str_replace('{year}', date('Y'), $formatted);
            $formatted = str_replace('{month}', date('m'), $formatted);
            $formatted = str_replace('{day}', date('d'), $formatted);

            return $prefix.$formatted;
        });
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
            $description .= ' - '.$plan->name;
        }

        $price = $service->calculatePrice();

        return $this->createInvoice($user, [
            [
                'quantity' => 1,
                'price' => $price,
                'description' => $description,
                'reference_id' => $service->id,
                'reference_type' => Service::class,
            ],
        ], $service->currency_code, $service->order_id);
    }

    public function markCancelled(Invoice $invoice): void
    {
        $invoice->update(['status' => Invoice::STATUS_CANCELLED]);
    }

    public function createSnapshot(Invoice $invoice): void
    {
        $totals = $this->calculateTotal($invoice);
        $user = $invoice->user;

        InvoiceSnapshot::create([
            'invoice_id' => $invoice->id,
            'name' => $invoice->number,
            'properties' => $user->properties()
                ->whereHas('parent_property', fn ($q) => $q->where('show_on_invoice', true))
                ->pluck('value', 'key')
                ->toArray(),
            'tax_name' => $totals['tax_name'],
            'tax_rate' => $totals['tax_rate'],
            'tax_country' => $user ? $this->tax->getUserCountry($user) : null,
            'bill_to' => $user ? $user->fullName().' <'.$user->email.'>' : null,
        ]);
    }
}
