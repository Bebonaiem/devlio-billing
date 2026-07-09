<?php

namespace App\Http\Controllers;

use App\Exceptions\CartEmptyException;
use App\Exceptions\InvalidCouponException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidPlanException;
use App\Exceptions\ProductUnavailableException;
use App\Exceptions\UserLimitExceededException;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\CreditService;
use App\Services\CurrencyService;
use App\Services\InvoiceService;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly CurrencyService $currency,
        private readonly CreditService $credit,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->with(['items.product', 'items.plan.prices', 'coupon'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart->items as $item) {
            $priceModel = $item->plan->prices()
                ->where('currency_code', $currencyCode)
                ->first() ?? $item->plan->prices()->first();

            $price = $priceModel ? (float) $priceModel->price : 0.0;
            $setupFee = $priceModel ? (float) $priceModel->setup_fee : 0.0;
            $item->subtotal = ($price * $item->quantity) + ($setupFee * $item->quantity);
            $subtotal += $item->subtotal;
        }

        $discount = 0.0;
        if ($cart->coupon) {
            if ($cart->coupon->type === 'percentage') {
                $discount = round($subtotal * ($cart->coupon->value / 100), 2);
            } else {
                $discount = min($cart->coupon->value, $subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        $creditBalance = $this->credit->getBalance($user, $currencyCode);
        $currency = Currency::where('code', $currencyCode)->first();
        $paymentGateways = \App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get();

        return view('checkout.index', compact(
            'cart', 'subtotal', 'discount', 'total', 'creditBalance', 'currency', 'paymentGateways'
        ));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'gateway' => 'required|exists:extensions,id',
            'apply_credit' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            $result = $this->checkout->processCart($cart, $user);

            if (!empty($validated['apply_credit']) && $result['invoice']) {
                $this->credit->applyToInvoice($user, $result['invoice']);

                $totals = app(InvoiceService::class)->calculateTotal($result['invoice']);
                $creditsApplied = (float) $result['invoice']->transactions()
                    ->where('is_credit_transaction', true)
                    ->where('status', 'succeeded')
                    ->sum('amount');
                $remaining = round(max(0, $totals['total'] - $creditsApplied), 2);

                if ($remaining <= 0) {
                    $result['invoice']->update(['status' => 'paid']);
                    app(InvoiceService::class)->createSnapshot($result['invoice']);
                    $this->activateInvoiceServices($result['invoice']);
                    return redirect()->route('checkout.success', ['invoice' => $result['invoice']->id]);
                }
            }

            if ($result['invoice']) {
                return redirect()->route('checkout.pay', ['invoice' => $result['invoice']->id]);
            }

            return redirect()->route('dashboard.index')
                ->with('success', 'Order placed successfully!');
        } catch (CartEmptyException $e) {
            return back()->with('error', $e->getMessage());
        } catch (ProductUnavailableException $e) {
            return back()->with('error', $e->getMessage());
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        } catch (UserLimitExceededException $e) {
            return back()->with('error', $e->getMessage());
        } catch (InvalidPlanException $e) {
            return back()->with('error', $e->getMessage());
        } catch (InvalidCouponException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process checkout. Please try again.');
        }
    }

    public function pay(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        if ($invoice->isPaid()) {
            return redirect()->route('dashboard.index')
                ->with('success', 'Invoice is already paid.');
        }

        $invoice->load(['items', 'currency', 'transactions']);

        $invoiceService = app(InvoiceService::class);
        $totals = $invoiceService->calculateTotal($invoice);
        $creditsApplied = (float) $invoice->transactions()
            ->where('is_credit_transaction', true)
            ->where('status', 'succeeded')
            ->sum('amount');
        $remaining = round(max(0, $totals['total'] - $creditsApplied), 2);

        $currencies = Currency::where('enabled', true)->get();
        $paymentGateways = Extension::where('type', 'gateway')->where('enabled', true)->get();

        return view('checkout.pay', compact(
            'invoice', 'totals', 'creditsApplied', 'remaining', 'currencies', 'paymentGateways'
        ));
    }

    public function processPay(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'gateway' => 'required|exists:extensions,id',
        ]);

        $user = Auth::user();
        $invoice = Invoice::with(['items', 'transactions'])->findOrFail($validated['invoice_id']);

        if ($invoice->user_id !== $user->id) {
            abort(403);
        }

        if ($invoice->isPaid()) {
            return redirect()->route('dashboard.index')
                ->with('success', 'Invoice is already paid.');
        }

        $gateway = Extension::findOrFail($validated['gateway']);
        $invoiceService = app(InvoiceService::class);
        $totals = $invoiceService->calculateTotal($invoice);
        $creditsApplied = (float) $invoice->transactions()
            ->where('is_credit_transaction', true)
            ->where('status', 'succeeded')
            ->sum('amount');
        $remaining = round(max(0, $totals['total'] - $creditsApplied), 2);

        if ($remaining <= 0) {
            $invoice->update(['status' => 'paid']);
            $invoiceService->createSnapshot($invoice);
            $this->activateInvoiceServices($invoice);
            return redirect()->route('checkout.success', ['invoice' => $invoice->id]);
        }

        return match ($gateway->extension) {
            'stripe' => $this->processStripePayment($invoice, $remaining, $gateway),
            'paypal' => $this->processPayPalPayment($invoice, $remaining, $gateway),
            'credit' => $this->processCreditPayment($invoice, $remaining, $gateway, $user),
            default => back()->with('error', 'Unsupported payment gateway.'),
        };
    }

    private function processStripePayment(Invoice $invoice, float $amount, Extension $gateway)
    {
        $stripe = app(StripeService::class);
        $successUrl = route('checkout.success', ['invoice' => $invoice->id]) . '&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('checkout.cancel');

        $session = $stripe->createPaymentSession(
            $invoice,
            $amount,
            $gateway->id,
            $successUrl,
            $cancelUrl
        );

        if (!$session || !$session->url) {
            return back()->with('error', 'Failed to create Stripe payment session. Please try again.');
        }

        return redirect()->away($session->url);
    }

    private function processPayPalPayment(Invoice $invoice, float $amount, Extension $gateway)
    {
        $paypal = app(PayPalService::class);
        $returnUrl = route('checkout.success', ['invoice' => $invoice->id]);
        $cancelUrl = route('checkout.cancel');

        $order = $paypal->createOrder($invoice, $returnUrl, $cancelUrl);

        if (!$order || !$order['approval_url']) {
            return back()->with('error', 'Failed to create PayPal order. Please try again.');
        }

        session(['paypal_order_id' => $order['id'], 'paypal_gateway_id' => $gateway->id]);

        return redirect()->away($order['approval_url']);
    }

    private function processCreditPayment(Invoice $invoice, float $amount, Extension $gateway, $user)
    {
        if ($this->credit->deduct($user, $amount, $invoice->currency_code)) {
            $transaction = InvoiceTransaction::create([
                'invoice_id' => $invoice->id,
                'gateway_id' => $gateway->id,
                'amount' => $amount,
                'fee' => 0,
                'transaction_id' => 'CREDIT-' . strtoupper(uniqid()),
                'status' => 'succeeded',
                'is_credit_transaction' => true,
            ]);

            app(InvoiceService::class)->markPaid($invoice, $transaction);

            return redirect()->route('checkout.success', ['invoice' => $invoice->id]);
        }

        return back()->with('error', 'Insufficient credit balance.');
    }

    private function activateInvoiceServices(Invoice $invoice): void
    {
        $items = $invoice->items()->whereNotNull('reference_id')->get();
        foreach ($items as $item) {
            if ($item->reference_type === \App\Models\Service::class) {
                $service = \App\Models\Service::find($item->reference_id);
                if ($service && $service->status === 'pending') {
                    $service->update(['status' => 'active']);
                    try {
                        app(\App\Services\ServiceService::class)->activateService($service);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            }
        }
    }

    public function success(Request $request)
    {
        $invoiceId = $request->query('invoice');
        $invoice = Invoice::where('id', $invoiceId)->first();

        if ($invoice && $invoice->user_id === Auth::id()) {
            $invoice->load(['items']);
        }

        return view('checkout.success', compact('invoice'));
    }

    public function cancel(Request $request)
    {
        return view('checkout.cancel');
    }

    public function setCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|exists:currencies,code',
        ]);

        session(['currency' => $validated['currency']]);

        return back()->with('success', 'Currency updated.');
    }
}
