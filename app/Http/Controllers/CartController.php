<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ConfigOption;
use App\Models\Plan;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->firstOrCreate([
                'user_id' => $user->id,
                'currency_code' => $currencyCode,
            ], [
                'ulid' => Str::ulid(),
            ]);

        $cart->load(['items.product', 'items.plan.prices', 'coupon']);

        $items = $cart->items;
        $subtotal = 0;

        foreach ($items as $item) {
            $price = $this->getPriceForPlan($item->plan, $currencyCode);
            $item->formatted_price = $price['price'];
            $item->formatted_setup_fee = $price['setup_fee'];
            $item->subtotal = ($price['price'] * $item->quantity) + ($price['setup_fee'] * $item->quantity);
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

        $currency = Currency::where('code', $currencyCode)->first();

        return view('cart.index', compact('cart', 'items', 'subtotal', 'discount', 'total', 'currency'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'quantity' => 'nullable|integer|min:1|max:10',
            'config_options' => 'nullable|array',
            'config_options.*' => 'integer|exists:config_options,id',
        ]);

        $user = Auth::user();
        $plan = Plan::with('priceable')->findOrFail($validated['plan_id']);
        $product = $plan->priceable;
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        if (!$product || !$product->enabled) {
            return back()->with('error', 'This product is no longer available.');
        }

        if ($product->stock !== null && $product->stock < ($validated['quantity'] ?? 1)) {
            return back()->with('error', 'Insufficient stock for this product.');
        }

        if ($product->per_user_limit !== null) {
            $existingCount = \App\Models\Service::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->count();

            if ($existingCount + ($validated['quantity'] ?? 1) > $product->per_user_limit) {
                return back()->with('error', 'Per-user limit exceeded for this product.');
            }
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'currency_code' => $currencyCode,
        ], [
            'ulid' => Str::ulid(),
        ]);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('plan_id', $plan->id)
            ->get()
            ->first(fn ($item) => $item->config_options == ($validated['config_options'] ?? []));

        if ($existingItem) {
            if (!$product->allow_quantity) {
                return back()->with('error', 'Only one of this item is allowed.');
            }
            $existingItem->increment('quantity', $validated['quantity'] ?? 1);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'plan_id' => $plan->id,
                'config_options' => $validated['config_options'] ?? [],
                'quantity' => $validated['quantity'] ?? 1,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Added to cart successfully!');
    }

    public function update(Request $request, CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $item->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Cart updated!');
    }

    public function remove(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        if ($cart) {
            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);
        }

        return back()->with('success', 'Cart cleared!');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        $coupon = \App\Models\Coupon::where('code', $validated['code'])->first();

        if (!$coupon || !$coupon->isActive()) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        $couponService = app(\App\Services\CouponService::class);
        if (!$couponService->validate($coupon, $user)) {
            return back()->with('error', 'Coupon code cannot be applied.');
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $cart = Cart::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        if ($cart) {
            $cart->update(['coupon_id' => null]);
        }

        return back()->with('success', 'Coupon removed.');
    }

    private function getPriceForPlan(Plan $plan, string $currencyCode): array
    {
        $prices = $plan->prices;
        $priceModel = $prices->firstWhere('currency_code', $currencyCode) ?? $prices->first();

        return [
            'price' => $priceModel ? (float) $priceModel->price : 0.0,
            'setup_fee' => $priceModel ? (float) $priceModel->setup_fee : 0.0,
        ];
    }
}
