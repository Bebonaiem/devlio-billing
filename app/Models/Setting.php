<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'encrypted',
        'settingable_id',
        'settingable_type',
    ];

    protected function casts(): array
    {
        return [
            'encrypted' => 'boolean',
            'settingable_id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Setting $setting) {
            if ($setting->encrypted && ! empty($setting->value)) {
                $setting->value = Crypt::encryptString($setting->value);
            } else {
                $setting->value = $setting->castValue($setting->value);
            }
        });

        static::retrieved(function (Setting $setting) {
            if ($setting->encrypted && ! empty($setting->value)) {
                try {
                    $setting->value = Crypt::decryptString($setting->value);
                } catch (\Throwable) {
                    $setting->value = null;
                }
            } else {
                $setting->value = $setting->castValue($setting->value, true);
            }
        });

        static::saved(function (Setting $setting) {
            if (is_null($setting->settingable_type)) {
                Cache::forget('settings');
                SettingsProvider::getSettings(true);
            }
        });
    }

    protected function castValue(mixed $value, bool $reverse = false): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if ($this->type === 'boolean') {
            return $reverse ? ($value ? '1' : '0') : (bool) $value;
        }

        if ($this->type === 'integer') {
            return $reverse ? (string) $value : (int) $value;
        }

        if ($this->type === 'float') {
            return $reverse ? (string) $value : (float) $value;
        }

        if ($this->type === 'array') {
            return $reverse ? json_encode($value) : json_decode($value, true);
        }

        return $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::get('settings', []);

        if (isset($settings[$key])) {
            return $settings[$key];
        }

        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allAsArray(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
