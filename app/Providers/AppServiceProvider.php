<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            $settings = Setting::all()->pluck('value', 'key');

            if ($settings->get('smtp_host')) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->get('smtp_host'),
                    'mail.mailers.smtp.port' => (int) ($settings->get('smtp_port') ?? 587),
                    'mail.mailers.smtp.username' => $settings->get('smtp_username') ?? '',
                    'mail.mailers.smtp.password' => $settings->get('smtp_password') ?? '',
                    'mail.mailers.smtp.encryption' => $settings->get('smtp_encryption') ?? 'tls',
                    'mail.from.address' => $settings->get('mail_from_address') ?? config('mail.from.address'),
                    'mail.from.name' => $settings->get('site_name') ?? config('mail.from.name'),
                ]);
            }

            if ($settings->get('pterodactyl_url')) {
                config([
                    'services.pterodactyl.panel_url' => $settings->get('pterodactyl_url'),
                    'services.pterodactyl.api_key' => $settings->get('pterodactyl_api_key') ?? '',
                ]);
            }

            if ($settings->get('paypal_client_id')) {
                config([
                    'services.paypal.client_id' => $settings->get('paypal_client_id'),
                    'services.paypal.secret' => $settings->get('paypal_secret') ?? '',
                    'services.paypal.webhook_id' => $settings->get('paypal_webhook_id') ?? '',
                    'services.paypal.mode' => $settings->get('paypal_sandbox') === '1' ? 'sandbox' : 'live',
                ]);
            }

            if ($settings->get('stripe_key')) {
                config([
                    'services.stripe.public_key' => $settings->get('stripe_key'),
                    'services.stripe.secret_key' => $settings->get('stripe_secret') ?? '',
                    'services.stripe.webhook_secret' => $settings->get('stripe_webhook_secret') ?? '',
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Could not load settings from database', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
