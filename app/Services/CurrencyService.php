<?php
namespace App\Services;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    public function getDefault(): Currency
    {
        return Cache::remember('default_currency', 3600, function () {
            return Currency::where('code', config('billing.default_currency', 'USD'))->firstOrFail();
        });
    }

    public function getByCode(string $code): ?Currency
    {
        return Currency::where('code', $code)->first();
    }

    public function getEnabled(): Collection
    {
        return Currency::where('enabled', true)->get();
    }

    public function format(float $amount, string $currencyCode = 'USD'): string
    {
        $currency = $this->getByCode($currencyCode);

        if (! $currency) {
            return number_format($amount, 2, '.', '').' '.$currencyCode;
        }

        $formatted = number_format($amount, 2, '.', '');

        return $currency->prefix.$formatted.$currency->suffix;
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = config('currency.rates', ['USD' => 1.0]);
        $fromRate = $rates[$from] ?? 1.0;
        $toRate = $rates[$to] ?? 1.0;

        if ($fromRate <= 0) {
            return $amount;
        }

        $usdAmount = $amount / $fromRate;

        return round($usdAmount * $toRate, 2);
    }

    public function getRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $rates = config('currency.rates', ['USD' => 1.0]);
        $fromRate = $rates[$from] ?? 1.0;
        $toRate = $rates[$to] ?? 1.0;

        if ($fromRate <= 0) {
            return 1.0;
        }

        return round($toRate / $fromRate, 6);
    }
}
