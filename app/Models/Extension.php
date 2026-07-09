¿<?php
namespace App\Models;

use App\Attributes\ExtensionMeta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;

class Extension extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'enabled',
        'extension',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    protected function path(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->type).'s/'.ucfirst($this->extension));
    }

    protected function namespace(): Attribute
    {
        return Attribute::get(fn () => '\\App\\Extensions\\'.ucfirst($this->type).'s\\'.ucfirst($this->extension));
    }

    protected function meta(): Attribute
    {
        return Attribute::get(function () {
            $namespace = $this->namespace;

            if (! class_exists($namespace)) {
                return null;
            }

            $reflection = new ReflectionClass($namespace);
            $attributes = $reflection->getAttributes(ExtensionMeta::class);

            return ! empty($attributes) ? $attributes[0]->newInstance() : null;
        });
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
