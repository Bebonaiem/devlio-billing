<?php
namespace App\Services;

use App\Models\Property;
use App\Models\TaxRate;
use App\Models\User;

class TaxService
{
    public function calculate(float $amount, string $country = 'all'): array
    {
        $taxRate = TaxRate::where(function ($query) use ($country) {
            $query->where('country', $country)
                ->orWhere('country', 'all');
        })->orderByRaw('CASE WHEN country = ? THEN 0 ELSE 1 END', [$country])
            ->first();

        if (! $taxRate || $taxRate->rate <= 0) {
            return [
                'tax_amount' => 0.0,
                'tax_rate' => 0.0,
                'tax_name' => '',
            ];
        }

        $taxAmount = round($amount * ($taxRate->rate / 100), 2);

        return [
            'tax_amount' => $taxAmount,
            'tax_rate' => (float) $taxRate->rate,
            'tax_name' => $taxRate->name,
        ];
    }

    public function getUserCountry(User $user): string
    {
        $property = Property::where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('key', 'country')
            ->first();

        if ($property && $property->value) {
            return $property->value;
        }

        return config('billing.default_country', 'all');
    }

    public function calculateForUser(float $amount, User $user): array
    {
        $country = $this->getUserCountry($user);

        return $this->calculate($amount, $country);
    }
}
