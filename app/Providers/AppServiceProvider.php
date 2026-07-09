<?php

namespace App\Providers;

use App\Models\Setting;
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

            if ($settings->has('smtp_host') && $settings->get('smtp_host')) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->get('smtp_host'),
                    'mail.mailers.smtp.port' => (int) ($settings->get('smtp_port') ?? 587),
                    'mail.mailers.smtp.username' => $settings->get('smtp_username') ?? '',
                    'mail.mailers.smtp.password' => $settings->get('smtp_password') ?? '',
                    'mail.mailers.smtp.encryption' => $settings->get('smtp_encryption') ?? 'tls',
                    'mail.from.address' => $settings->get('mail_from_address') ?? env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                    'mail.from.name' => $settings->get('site_name') ?? env('APP_NAME', 'Laravel'),
                ]);
            }

            if ($settings->has('pterodactyl_url') && $settings->get('pterodactyl_url')) {
                config([
                    'services.pterodactyl.panel_url' => $settings->get('pterodactyl_url'),
                    'services.pterodactyl.api_key' => $settings->get('pterodactyl_api_key') ?? '',
                ]);
            }

            if ($settings->has('paypal_client_id') && $settings->get('paypal_client_id')) {
                config([
                    'services.paypal.client_id' => $settings->get('paypal_client_id'),
                    'services.paypal.secret' => $settings->get('paypal_secret') ?? '',
                    'services.paypal.webhook_id' => $settings->get('paypal_webhook_id') ?? '',
                    'services.paypal.sandbox' => $settings->get('paypal_sandbox') === '1',
                ]);
            }

            if ($settings->has('stripe_key') && $settings->get('stripe_key')) {
                config([
                    'services.stripe.key' => $settings->get('stripe_key'),
                    'services.stripe.secret' => $settings->get('stripe_secret') ?? '',
                ]);
            }
        } catch (\Exception $e) {
            // DB not ready yet (migrations not run)
        }
    }
}