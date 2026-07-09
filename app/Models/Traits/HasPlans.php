<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Collection;

trait HasPlans
{
    public function plans()
    {
        return $this->morphMany(Plan::class, 'priceable');
    }

    public function availablePlans(?string $currency = null): Collection
    {
        $plans = $this->plans;

        if ($currency) {
            $plans = $plans->filter(fn ($plan) => $plan->prices()->where('currency_code', $currency)->exists());
        }

        return $plans->sortBy('sort');
    }

    public function price(int $planId, int $billingPeriod, string $billingUnit, ?string $currency = null): ?Price
    {
        $plan = $this->plans()->find($planId);

        if (! $plan) {
            return null;
        }

        return $plan->price($currency);
    }
}
