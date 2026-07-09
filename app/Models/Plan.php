<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Plan extends Model
{
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

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
        ];
    }

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
}
