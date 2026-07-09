<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTransaction;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceConfig;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly InvoiceService $invoice,
        private readonly CouponService $coupon,
        private readonly TaxService $tax,
        private readonly CreditService $credit,
        private readonly CurrencyService $currency,
        private readonly ServiceService $service,
    ) {}

    public function processCart(Cart $cart, User $user): array
    {
        return DB::transaction(function () use ($cart, $user) {
            $cart->load(['items.product', 'items.plan', 'coupon']);

            $this->validateCart($cart, $user);

            $order = Order::create([
                'user_id' => $user->id,
                'currency_code' => $cart->currency_code,
            ]);

            $services = new Collection();
            $invoiceItems = [];
            $totalAmount = 0.0;

            foreach ($cart->items as $cartItem) {
                $result = $this->processCartItem($cartItem, $order, $user, $cart);
                $services->push($result['service']);
                $invoiceItems = array_merge($invoiceItems, $result['invoice_items']);
                $totalAmount += $result['subtotal'];
            }

            $taxResult = $this->tax->calculate($totalAmount, $this->tax->getUserCountry($user));
            $totalAmount = round($totalAmount + $taxResult['tax_amount'], 2);

            $discount = 0.0;
            if ($cart->coupon) {
                $discountResult = $this->coupon->apply($cart->coupon, $totalAmount);
                $discount = $discountResult['discount'];
                $totalAmount = $discountResult['total'];
            }

            $invoice = null;
            if ($totalAmount > 0) {
                $invoice = $this->createCheckoutInvoice(
                    $user,
                    $order,
                    $invoiceItems,
                    $taxResult,
                    $discount,
                    $cart->currency_code
                );
            }

            $this->finalizeServices($services, $invoice);

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return [
                'order' => $order,
                'invoice' => $invoice,
                'services' => $services,
            ];
        });
    }

    private function validateCart(Cart $cart, User $user): void
    {
        if ($cart->items->isEmpty()) {
            throw new \App\Exceptions\CartEmptyException('Cart is empty');
        }

        foreach ($cart->items as $cartItem) {
            $product = $cartItem->product;

            if (! $product || ! $product->enabled) {
                throw new \App\Exceptions\ProductUnavailableException(
                    "Product is not available: {$cartItem->product_id}"
                );
            }

            if ($product->stock !== null && $product->stock < $cartItem->quantity) {
                throw new \App\Exceptions\InsufficientStockException(
                    "Insufficient stock for product: {$product->name}"
                );
            }

            if ($product->per_user_limit !== null) {
                $existingCount = Service::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->count();

                if ($existingCount + $cartItem->quantity > $product->per_user_limit) {
                    throw new \App\Exceptions\UserLimitExceededException(
                        "Per-user limit exceeded for product: {$product->name}"
                    );
                }
            }

            if (! $cartItem->plan) {
                throw new \App\Exceptions\InvalidPlanException(
                    "Invalid plan selected for product: {$product->name}"
                );
            }
        }

        if ($cart->coupon) {
            if (! $this->coupon->validate($cart->coupon, $user)) {
                throw new \App\Exceptions\InvalidCouponException('Invalid coupon code');
            }
        }
    }

    private function processCartItem(CartItem $cartItem, Order $order, User $user, Cart $cart): array
    {
        $product = $cartItem->product;
        $plan = $cartItem->plan;
        $price = $this->getPriceForPlan($plan, $order->currency_code);

        $service = Service::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'quantity' => $cartItem->quantity,
            'price' => $price['price'],
            'currency_code' => $order->currency_code,
            'status' => 'pending',
            'coupon_id' => $cart->coupon_id,
            'label' => $this->generateServiceLabel($product, $plan),
        ]);

        $this->createServiceConfigs($service, $cartItem);

        $subtotal = ($price['price'] * $cartItem->quantity) + ($price['setup_fee'] * $cartItem->quantity);

        $invoiceItems = [];

        if ($price['setup_fee'] > 0) {
            $invoiceItems[] = [
                'quantity' => 1,
                'price' => $price['setup_fee'] * $cartItem->quantity,
                'description' => "Setup Fee - {$product->name} ({$plan->name})",
                'reference_id' => $service->id,
                'reference_type' => Service::class,
            ];
        }

        $invoiceItems[] = [
            'quantity' => $cartItem->quantity,
            'price' => $price['price'],
            'description' => "{$product->name} - {$plan->name}",
            'reference_id' => $service->id,
            'reference_type' => Service::class,
        ];

        if ($product->stock !== null) {
            $product->decrement('stock', $cartItem->quantity);
        }

        return [
            'service' => $service,
            'invoice_items' => $invoiceItems,
            'subtotal' => $subtotal,
        ];
    }

    private function getPriceForPlan($plan, string $currencyCode): array
    {
        $priceModel = $plan->prices()
            ->where('currency_code', $currencyCode)
            ->first();

        if (! $priceModel) {
            $priceModel = $plan->prices()->first();
        }

        return [
            'price' => $priceModel ? (float) $priceModel->price : 0.0,
            'setup_fee' => $priceModel ? (float) $priceModel->setup_fee : 0.0,
        ];
    }

    private function createServiceConfigs(Service $service, CartItem $cartItem): void
    {
        $configOptions = $cartItem->config_options ?? [];

        foreach ($configOptions as $configOptionId => $configValueId) {
            ServiceConfig::create([
                'service_id' => $service->id,
                'config_option_id' => $configOptionId,
                'config_value_id' => $configValueId,
            ]);
        }
    }

    private function generateServiceLabel($product, $plan): string
    {
        return "{$product->name} - {$plan->name}";
    }

    private function createCheckoutInvoice(
        User $user,
        Order $order,
        array $invoiceItems,
        array $taxResult,
        float $discount,
        string $currencyCode
    ): Invoice {
        $subtotal = array_sum(array_map(fn ($item) => $item['price'] * ($item['quantity'] ?? 1), $invoiceItems));

        $invoice = Invoice::create([
            'number' => $this->invoice->generateNumber(),
            'user_id' => $user->id,
            'currency_code' => $currencyCode,
            'status' => 'pending',
            'due_at' => now()->addDays(config('billing.invoice_due_days', 7)),
        ]);

        foreach ($invoiceItems as $item) {
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
    }

    private function finalizeServices(Collection $services, ?Invoice $invoice): void
    {
        foreach ($services as $service) {
            $plan = $service->plan;

            if ($plan->type === 'free' || $plan->type === 'one-time') {
                $service->update(['status' => 'active']);

                if ($invoice) {
                    $this->invoice->markPaid($invoice, $this->createCreditTransaction($invoice));
                }
            }

            if (!$service->expires_at) {
                $service->update(['expires_at' => $this->service->getExpiryDate($plan)]);
            }

            if (!$service->server) {
                $this->service->activateService($service);
            }
        }
    }

    private function createCreditTransaction(Invoice $invoice): InvoiceTransaction
    {
        $totals = $this->invoice->calculateTotal($invoice);

        return InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'amount' => $totals['total'],
            'fee' => 0,
            'transaction_id' => 'FREE-' . strtoupper(uniqid()),
            'status' => 'succeeded',
            'is_credit_transaction' => true,
        ]);
    }
}
