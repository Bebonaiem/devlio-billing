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

    public function index(Plan $plan)
    {
        if (!$plan->is_active) {
            abort(404);
        }

        $plan->load('product');
        return view('checkout.index', compact('plan'));
    }

    public function process(Request $request, Plan $plan)
    {
        if (!$plan->is_active) {
            return back()->with('error', 'This plan is no longer available.');
        }

        $user = Auth::user();
        $gateway = $request->input('gateway', 'stripe');

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
            ]);

            $invoice = $this->billing->createInitialInvoice($order);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order. Please try again.');
        }

        if ($gateway === 'stripe') {
            $customerId = $user->paymentMethods()
                ->where('gateway', 'stripe')
                ->where('is_default', true)
                ->first()?->gateway_customer_id;

            $successUrl = route('checkout.success', ['order' => $order->id]);
            $cancelUrl = route('checkout.cancel', ['order' => $order->id]);

            $session = $this->stripe->createCheckoutSession(
                $invoice,
                $successUrl,
                $cancelUrl,
                $customerId
            );

            if ($session) {
                return redirect($session->url);
            }

            return back()->with('error', 'Failed to create checkout session.');
        }

        if ($gateway === 'paypal') {
            $returnUrl = route('checkout.success', ['order' => $order->id]);
            $cancelUrl = route('checkout.cancel', ['order' => $order->id]);

            $orderId = $this->paypal->createOrder($invoice, $returnUrl, $cancelUrl);

            if ($orderId) {
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

        // Provision the server after successful payment
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
