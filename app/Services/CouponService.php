<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function validate(Coupon $coupon, User $user, ?Product $product = null): bool
    {
        if (! $coupon->isActive()) {
            return false;
        }

        if ($coupon->max_uses !== null && $coupon->services()->count() >= $coupon->max_uses) {
            return false;
        }

        if ($coupon->max_uses_per_user !== null) {
            $userUsageCount = $coupon->services()
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $coupon->max_uses_per_user) {
                return false;
            }
        }

        if ($coupon->applies_to === 'specific' && $product) {
            $appliesToProduct = $coupon->products()->where('products.id', $product->id)->exists();

            if (! $appliesToProduct) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(Coupon $coupon, float $amount): float
    {
        if ($coupon->type === 'percentage') {
            return round($amount * ($coupon->value / 100), 2);
        }

        return min(round($coupon->value, 2), $amount);
    }

    public function apply(Coupon $coupon, float $amount): array
    {
        $discount = $this->calculateDiscount($coupon, $amount);
        $total = round($amount - $discount, 2);

        return [
            'discount' => max(0.0, $discount),
            'total' => max(0.0, $total),
        ];
    }

    public function getValidCouponsForProduct(Product $product, User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Coupon::where(function ($query) use ($product) {
            $query->where('applies_to', 'all')
                ->orWhereHas('products', function ($q) use ($product) {
                    $q->where('products.id', $product->id);
                });
        })->where(function ($query) {
            $query->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now());
        })->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        })->where(function ($query) use ($user) {
            $query->whereNull('max_uses')
                ->orWhereRaw('(SELECT COUNT(*) FROM services WHERE services.coupon_id = coupons.id) < coupons.max_uses');
        })->where(function ($query) use ($user) {
            $query->whereNull('max_uses_per_user')
                ->orWhereRaw('(SELECT COUNT(*) FROM services WHERE services.coupon_id = coupons.id AND services.user_id = ?) < coupons.max_uses_per_user', [$user->id]);
        })->get();
    }
}
