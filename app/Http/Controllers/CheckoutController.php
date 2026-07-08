<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Order;
use App\Jobs\ProvisionServer;
use App\Services\BillingService;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly BillingService $billing,
        private readonly StripeService $stripe,
        private readonly PayPalService $paypal,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $items = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            $plan = Plan::with('product')->find($item['plan_id']);
            if ($plan && $plan->is_active) {
                $quantity = $item['quantity'] ?? 1;
                $subtotal = $plan->price * $quantity;
                $items[$key] = [
                    'plan' => $plan,
                    'quantity' => $quantity,
                    'config' => $item['config'] ?? [],
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        return view('checkout.index', compact('items', 'total'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'gateway' => 'required|in:stripe,paypal',
        ]);

        $user = Auth::user();
        $gateway = $validated['gateway'];
        $orders = [];

        DB::beginTransaction();
        try {
            foreach ($cart as $key => $item) {
                $plan = Plan::with('product')->find($item['plan_id']);
                if (!$plan || !$plan->is_active) {
                    continue;
                }

                $quantity = $item['quantity'] ?? 1;
                for ($i = 0; $i < $quantity; $i++) {
                    $order = Order::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'status' => 'pending',
                    ]);

                    $invoice = $this->billing->createInitialInvoice($order);
                    $orders[] = ['order' => $order, 'invoice' => $invoice];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create orders. Please try again.');
        }

        if (empty($orders)) {
            return back()->with('error', 'No valid items in cart.');
        }

        if ($gateway === 'stripe') {
            $customerId = $user->paymentMethods()
                ->where('gateway', 'stripe')
                ->where('is_default', true)
                ->first()?->gateway_customer_id;

            $successUrl = route('checkout.success', ['order' => $orders[0]['order']->id]);
            $cancelUrl = route('checkout.cancel', ['order' => $orders[0]['order']->id]);

            $session = $this->stripe->createCheckoutSession(
                $orders[0]['invoice'],
                $successUrl,
                $cancelUrl,
                $customerId
            );

            if ($session) {
                session()->forget('cart');
                return redirect($session->url);
            }

            return back()->with('error', 'Failed to create checkout session.');
        }

        if ($gateway === 'paypal') {
            $returnUrl = route('checkout.success', ['order' => $orders[0]['order']->id]);
            $cancelUrl = route('checkout.cancel', ['order' => $orders[0]['order']->id]);

            $orderId = $this->paypal->createOrder($orders[0]['invoice'], $returnUrl, $cancelUrl);

            if ($orderId) {
                session()->forget('cart');
                return redirect('https://www.paypal.com/checkoutnow?token=' . $orderId);
            }

            return back()->with('error', 'Failed to create PayPal order.');
        }

        return back()->with('error', 'Invalid payment gateway.');
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'pending') {
            ProvisionServer::dispatch($order);
        }

        return view('checkout.success', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.cancel', compact('order'));
    }
}
