<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'currency_code',
    ];

    public bool $sendCreateEmail = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->services->sum(function ($service) {
            return $service->price * $service->quantity;
        });
    }

    public function getFormattedTotalAttribute(): string
    {
        $total = $this->total;
        $currency = $this->currency;

        if (! $currency) {
            return number_format($total, 2);
        }

        return $currency->prefix.number_format($total, 2).$currency->suffix;
    }

    public function getInvoicesAttribute()
    {
        $invoiceIds = $this->services
            ->flatMap(fn ($service) => $service->invoiceItems->pluck('invoice_id'))
            ->unique()
            ->values();

        return Invoice::whereIn('id', $invoiceIds)->get();
    }
}
