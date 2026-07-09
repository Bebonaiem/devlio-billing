<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Plan extends Model
{
    const TYPE_FREE = 'free';

    const TYPE_ONE_TIME = 'one-time';

    const TYPE_RECURRING = 'recurring';

    protected $fillable = [
        'name',
        'type',
        'billing_period',
        'billing_unit',
        'sort',
        'priceable_id',
        'priceable_type',
        'memory',
        'cpu',
        'disk',
        'swap',
        'databases',
        'backups',
        'allocations',
        'nest_id',
        'egg_id',
        'description',
    ];

    protected $casts = [
        'sort' => 'integer',
        'billing_period' => 'integer',
    ];

    public $timestamps = false;

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function isFree(): bool
    {
        return $this->type === self::TYPE_FREE;
    }

    public function isOneTime(): bool
    {
        return $this->type === self::TYPE_ONE_TIME;
    }

    public function isRecurring(): bool
    {
        return $this->type === self::TYPE_RECURRING;
    }

    public function price(?string $currency = null): ?Price
    {
        if ($this->isFree()) {
            return new Price(['price' => 0, 'setup_fee' => 0, 'currency_code' => $currency ?? 'USD']);
        }

        $query = $this->prices();

        if ($currency) {
            $query->where('currency_code', $currency);
        }

        return $query->first();
    }

    public function getBillingDurationAttribute(): int
    {
        if ($this->isFree() || $this->isOneTime()) {
            return 0;
        }

        $unitDays = match ($this->billing_unit) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30,
        };

        return $unitDays * $this->billing_period;
    }
}
