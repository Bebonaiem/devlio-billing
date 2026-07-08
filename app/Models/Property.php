<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Property extends Model
{
    protected $fillable = [
        'custom_property_id',
        'model_id',
        'model_type',
        'key',
        'value',
        'name',
    ];

    public function customProperty(): BelongsTo
    {
        return $this->belongsTo(CustomProperty::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
