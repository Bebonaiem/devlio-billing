<?php
namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasPlans;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'image',
        'enabled',
        'per_user_limit',
        'stock',
        'hidden',
        'allow_quantity',
        'server_id',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'per_user_limit' => 'integer',
            'stock' => 'integer',
            'hidden' => 'boolean',
            'allow_quantity' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function configOptions(): BelongsToMany
    {
        return $this->belongsToMany(ConfigOption::class, 'config_option_products')
            ->where('hidden', false)
            ->orderBy('sort')
            ->orderBy('id', 'desc');
    }

    public function upgradableConfigOptions(): BelongsToMany
    {
        return $this->belongsToMany(ConfigOption::class, 'config_option_products')
            ->where('upgradable', true)
            ->orderBy('sort');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function upgrades(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_upgrades', 'product_id', 'upgrade_id');
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public function setSetting(string $key, mixed $value): void
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
