<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasProperties
{
    public function properties(): MorphMany
    {
        return $this->morphMany(Property::class, 'model');
    }

    public function getProperty(string $key, mixed $default = null): mixed
    {
        $property = $this->properties()->where('key', $key)->first();

        return $property?->value ?? $default;
    }

    public function setProperty(string $key, mixed $value, ?int $customPropertyId = null): void
    {
        $this->properties()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'custom_property_id' => $customPropertyId,
                'name' => $key,
            ]
        );
    }

    public function removeProperty(string $key): void
    {
        $this->properties()->where('key', $key)->delete();
    }
}
