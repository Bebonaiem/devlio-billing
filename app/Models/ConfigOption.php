<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConfigOption extends Model
{
    protected $fillable = [
        'name',
        'description',
        'env_variable',
        'type',
        'sort',
        'hidden',
        'parent_id',
        'upgradable',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'hidden' => 'boolean',
            'upgradable' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ConfigOption::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ConfigOption::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'config_option_products');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ConfigOption::class, 'parent_id');
    }

    public function serviceConfigs(): HasMany
    {
        return $this->hasMany(ServiceConfig::class);
    }
}
