<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
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

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'priceable_id')
            ->where('priceable_type', Product::class);
    }

    public function configOptions(): BelongsToMany
    {
        return $this->belongsToMany(ConfigOption::class, 'config_option_products');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
