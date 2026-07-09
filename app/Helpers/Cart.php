<?php

namespace App\Helpers;

use App\Exceptions\InvalidCouponException;
use App\Models\Cart as CartModel;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class Cart
{
    const DEFAULT_MAX_ITEMS = 15;

    const DEFAULT_RATE_LIMIT_MAX_ATTEMPTS = 10;

    const DEFAULT_RATE_LIMIT_DECAY_MINUTES = 1;

    public static function getOnce(): ?CartModel
    {
        $ulid = Cookie::get('cart');

        if (! $ulid) {
            return null;
        }

        return CartModel::where('ulid', $ulid)
            ->with(['items.product', 'items.plan', 'coupon', 'currency'])
            ->first();
    }

    public static function get(): ?CartModel
    {
        return static::getOnce();
    }

    public static function createCart(): CartModel
    {
        $ulid = Str::ulid()->toString();
        $user = auth()->user();

        $cart = CartModel::create([
            'ulid' => $ulid,
            'user_id' => $user?->id,
            'currency_code' => session('currency', config('settings.default_currency', 'USD')),
        ]);

        Cookie::queue('cart', $ulid, 60 * 24 * 30);

        return $cart;
    }

    public static function add(Product $product, Plan $plan, array $configOptions = [], array $checkoutConfig = [], int $quantity = 1, ?string $key = null): int
    {
        $cart = static::get() ?? static::createCart();

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'plan_id' => $plan->id,
            ],
            [
                'config_options' => $configOptions,
                'checkout_config' => $checkoutConfig,
                'quantity' => $quantity,
            ]
        );

        return $cartItem->id;
    }

    public static function remove(int $itemId): void
    {
        $cart = static::get();

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
        }
    }

    public static function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = static::get();

        if (! $cart) {
            return;
        }

        if ($quantity < 1) {
            static::remove($itemId);

            return;
        }

        $cart->items()->where('id', $itemId)->update(['quantity' => $quantity]);
    }

    public static function applyCoupon(string $code): void
    {
        $cart = static::get();

        if (! $cart) {
            return;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isActive()) {
            throw new InvalidCouponException('Invalid or expired coupon code.');
        }

        $cart->update(['coupon_id' => $coupon->id]);
    }

    public static function removeCoupon(): void
    {
        $cart = static::get();

        if ($cart) {
            $cart->update(['coupon_id' => null]);
        }
    }

    public static function clear(): void
    {
        $cart = static::get();

        if ($cart) {
            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);
        }
    }
}
