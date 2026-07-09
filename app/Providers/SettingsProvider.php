<?php
namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SettingsProvider extends ServiceProvider
{
    public static bool $loaded = false;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        self::getSettings();
    }

    public static function getSettings(bool $force = false): void
    {
        if (self::$loaded && ! $force) {
            return;
        }

        try {
            $settings = Cache::remember('settings', 3600, function () {
                return Setting::whereNull('settingable_type')
                    ->get()
                    ->pluck('value', 'key')
                    ->toArray();
            });

            config(['settings' => $settings]);

            self::applyConfigOverrides($settings);
            self::applyTimezone($settings);
            self::applyForceHttps($settings);

            self::$loaded = true;
        } catch (\Throwable $e) {
            Log::warning('Could not load settings from database', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected static function applyConfigOverrides(array $settings): void
    {
        $overrides = [
            'smtp_host' => 'mail.mailers.smtp.host',
            'smtp_port' => 'mail.mailers.smtp.port',
            'smtp_username' => 'mail.mailers.smtp.username',
            'smtp_password' => 'mail.mailers.smtp.password',
            'smtp_encryption' => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name' => 'mail.from.name',
            'company_name' => 'app.name',
        ];

        foreach ($overrides as $settingKey => $configKey) {
            if (isset($settings[$settingKey]) && ! empty($settings[$settingKey])) {
                $value = $settings[$settingKey];

                if ($configKey === 'app.name') {
                    config(['app.name' => $value]);
                } elseif (str_starts_with($configKey, 'mail.')) {
                    config([$configKey => $value]);
                }
            }
        }
    }

    protected static function applyTimezone(array $settings): void
    {
        if (isset($settings['timezone']) && ! empty($settings['timezone'])) {
            date_default_timezone_set($settings['timezone']);
        }
    }

    protected static function applyForceHttps(array $settings): void
    {
        if (isset($settings['force_https']) && $settings['force_https']) {
            $_SERVER['HTTPS'] = 'on';
        }
    }

    public static function flushCache(): void
    {
        Cache::forget('settings');
        self::$loaded = false;
        self::getSettings(true);
    }
}
