<?php

namespace App\Models;

use App\Models\Traits\HasProperties;
use App\Services\TaxService;
use Barryvdh\DomPDF\FacadesPDF;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Invoice extends Model implements AuditableContract
{
    use Auditable, HasProperties;

    const STATUS_PENDING = 'pending';

    const STATUS_PAID = 'paid';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'number',
        'user_id',
        'order_id',
        'currency_code',
        'due_at',
        'status',
    ];

    protected $casts = [
        'due_at' => 'date',
    ];

    public bool $sendCreateEmail = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvoiceTransaction::class);
    }

    public function snapshot(): HasOne
    {
        return $this->hasOne(InvoiceSnapshot::class);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('number', $value)->orWhere('id', $value)->firstOrFail();
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE;
    }

    public function total(): float
    {
        return (float) $this->items->sum(function (InvoiceItem $item) {
            return $item->quantity * $item->price;
        });
    }

    public function getFormattedTotalAttribute(): string
    {
        $total = $this->total();
        $currency = $this->currency;

        if (! $currency) {
            return number_format($total, 2);
        }

        return $currency->prefix.number_format($total, 2).$currency->suffix;
    }

    public function remaining(): float
    {
        $paid = (float) $this->transactions()
            ->where('status', 'succeeded')
            ->sum('amount');

        return max(0, $this->total() - $paid);
    }

    public function getFormattedRemainingAttribute(): string
    {
        $remaining = $this->remaining();
        $currency = $this->currency;

        if (! $currency) {
            return number_format($remaining, 2);
        }

        return $currency->prefix.number_format($remaining, 2).$currency->suffix;
    }

    public function tax(): float
    {
        if ($this->snapshot) {
            return (float) ($this->snapshot->tax_rate ?? 0);
        }

        $tax = app(TaxService::class)->calculateForUser($this->total(), $this->user);

        return $tax['tax_amount'] ?? 0;
    }

    public function getTaxRateAttribute(): float
    {
        if ($this->snapshot) {
            return (float) $this->snapshot->tax_rate;
        }

        $tax = app(TaxService::class)->calculateForUser($this->total(), $this->user);

        return $tax['tax_rate'] ?? 0;
    }

    public function getUserNameAttribute(): string
    {
        if ($this->snapshot) {
            return $this->snapshot->name ?? '';
        }

        return $this->user?->name ?? '';
    }

    public function getUserPropertiesAttribute(): array
    {
        if ($this->snapshot) {
            return $this->snapshot->properties ?? [];
        }

        return $this->user->properties()
            ->whereHas('parent_property', fn ($q) => $q->where('show_on_invoice', true))
            ->pluck('value', 'key')
            ->toArray();
    }

    public function getBillToAttribute(): string
    {
        if ($this->snapshot) {
            return $this->snapshot->bill_to ?? '';
        }

        return config('settings.bill_to_text', 'Bill To');
    }

    public function pdf(): PDF
    {
        return FacadesPDF::loadView('invoices.pdf', ['invoice' => $this]);
    }
}
