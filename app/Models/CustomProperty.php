<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomProperty extends Model
{
    protected $fillable = [
        'name',
        'description',
        'allowed_values',
        'show_on_invoice',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'allowed_values' => 'json',
            'show_on_invoice' => 'boolean',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
