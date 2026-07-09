¿<?php
namespace App\Models;

use App\Models\Traits\HasProperties;
use App\Services\TaxService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Service extends Model implements AuditableContract
{
    use Auditable, HasProperties;

    const STATUS_PENDING = 'pending';

    const STATUS_ACTIVE = 'active';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'expires_at',
        'subscription_id',
        'status',
        'coupon_id',
        'currency_code',
        'billing_agreement_id',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function configs(): HasMany
    {
        return $this->hasMany(ServiceConfig::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'reference_id')
            ->where('reference_type', Service::class);
    }

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, InvoiceItem::class, 'reference_id', 'invoice_id', 'id', 'id')
            ->where('invoice_items.reference_type', Service::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(ServiceCancellation::class);
    }

    public function upgrades(): HasMany
    {
        return $this->hasMany(ServiceUpgrade::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function billingAgreement(): BelongsTo
    {
        return $this->belongsTo(BillingAgreement::class);
    }

    public function server(): HasOne
    {
        return $this->hasOne(Server::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        $currency = $this->currency;

        if (! $currency) {
            return number_format($this->price * $this->quantity, 2);
        }

        return $currency->prefix.number_format($this->price * $this->quantity, 2).$currency->suffix;
    }

    public function getLabelAttribute(): string
    {
        return $this->attributes['label'] ?? "Service #{$this->id}";
    }

    public function getDescriptionAttribute(): string
    {
        if (! $this->plan || ! $this->plan->isRecurring()) {
            return $this->product?->name ?? 'Service';
        }

        $start = $this->created_at->format('M d, Y');
        $end = $this->expires_at?->format('M d, Y') ?? 'N/A';

        return "{$this->product->name} ({$start} - {$end})";
    }

    public function isCancellable(): bool
    {
        return $this->status !== self::STATUS_CANCELLED
            && ! $this->plan?->isFree()
            && ! $this->plan?->isOneTime()
            && ! $this->cancellations()->where('type', 'scheduled')->exists();
    }

    public function isUpgradable(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && ($this->product->upgrades->isNotEmpty() || $this->product->upgradableConfigOptions->isNotEmpty())
            && ! $this->upgrades()->where('status', ServiceUpgrade::STATUS_PENDING)->exists();
    }

    public function calculateNextDueDate(): ?Carbon
    {
        if (! $this->plan || $this->plan->isFree() || $this->plan->isOneTime()) {
            return null;
        }

        $date = $this->expires_at && $this->status === self::STATUS_ACTIVE
            ? $this->expires_at->copy()
            : now();

        return $date->addDays($this->plan->billingDuration);
    }

    public function calculatePrice(): float
    {
        $planPrice = $this->plan?->price($this->currency_code)?->price ?? 0;
        $price = (float) $planPrice;

        foreach ($this->configs as $config) {
            if ($config->configValue && $config->configValue->parent) {
                $price += (float) ($config->configValue->price ?? 0);
            }
        }

        if ($this->coupon) {
            $invoices = $this->invoices()->where('status', 'paid')->count() + 1;
            if ($this->coupon->recurring === null || $invoices <= $this->coupon->recurring) {
                $discount = $this->coupon->calculateDiscount($price);
                $price -= $discount;
            }
        }

        $tax = app(TaxService::class)->calculateForUser($price, $this->user);
        $price += $tax['tax_amount'] ?? 0;

        return round($price, 2);
    }

    public function productUpgrades(): Collection
    {
        return $this->product->upgrades->filter(function ($upgrade) {
            if ($upgrade->stock !== null && $upgrade->stock <= 0) {
                return false;
            }

            if ($this->plan->billing_period !== $upgrade->plans->first()->billing_period ||
                $this->plan->billing_unit !== $upgrade->plans->first()->billing_unit) {
                return false;
            }

            return true;
        });
    }
}
