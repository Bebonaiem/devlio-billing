<?php
namespace App\Services;

use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;

class Settings
{
    public static function settings(): array
    {
        return [
            'general' => [
                'company_name' => ['type' => 'text', 'description' => 'Your company name'],
                'company_email' => ['type' => 'text', 'description' => 'Support email address'],
                'app_url' => ['type' => 'url', 'description' => 'Application URL'],
                'app_name' => ['type' => 'text', 'description' => 'Application name shown in browser'],
                'logo' => ['type' => 'text', 'description' => 'Logo URL or path'],
                'favicon' => ['type' => 'text', 'description' => 'Favicon URL or path'],
                'tos_url' => ['type' => 'url', 'description' => 'Terms of Service URL'],
                'timezone' => ['type' => 'text', 'description' => 'Application timezone'],
            ],
            'security' => [
                'captcha_enabled' => ['type' => 'boolean', 'description' => 'Enable CAPTCHA on forms'],
                'force_https' => ['type' => 'boolean', 'description' => 'Force HTTPS connections'],
            ],
            'social-login' => [
                'oauth_google_enabled' => ['type' => 'boolean', 'description' => 'Enable Google OAuth'],
                'oauth_google_client_id' => ['type' => 'text', 'description' => 'Google Client ID'],
                'oauth_google_client_secret' => ['type' => 'text', 'description' => 'Google Client Secret', 'encrypted' => true],
                'oauth_github_enabled' => ['type' => 'boolean', 'description' => 'Enable GitHub OAuth'],
                'oauth_github_client_id' => ['type' => 'text', 'description' => 'GitHub Client ID'],
                'oauth_github_client_secret' => ['type' => 'text', 'description' => 'GitHub Client Secret', 'encrypted' => true],
                'oauth_discord_enabled' => ['type' => 'boolean', 'description' => 'Enable Discord OAuth'],
                'oauth_discord_client_id' => ['type' => 'text', 'description' => 'Discord Client ID'],
                'oauth_discord_client_secret' => ['type' => 'text', 'description' => 'Discord Client Secret', 'encrypted' => true],
            ],
            'tax' => [
                'tax_enabled' => ['type' => 'boolean', 'description' => 'Enable tax calculations'],
                'tax_type' => ['type' => 'select', 'description' => 'Tax calculation type', 'options' => ['inclusive', 'exclusive']],
            ],
            'mail' => [
                'smtp_host' => ['type' => 'text', 'description' => 'SMTP Host'],
                'smtp_port' => ['type' => 'integer', 'description' => 'SMTP Port', 'default' => 587],
                'smtp_username' => ['type' => 'text', 'description' => 'SMTP Username'],
                'smtp_password' => ['type' => 'text', 'description' => 'SMTP Password', 'encrypted' => true],
                'smtp_encryption' => ['type' => 'select', 'description' => 'SMTP Encryption', 'options' => ['tls', 'ssl'], 'default' => 'tls'],
                'mail_from_address' => ['type' => 'email', 'description' => 'From Email Address'],
                'mail_from_name' => ['type' => 'text', 'description' => 'From Name'],
            ],
            'tickets' => [
                'tickets_disabled' => ['type' => 'boolean', 'description' => 'Disable support tickets'],
                'ticket_departments' => ['type' => 'array', 'description' => 'Available departments'],
                'ticket_mail_piping' => ['type' => 'boolean', 'description' => 'Enable email piping for tickets'],
                'ticket_mail_host' => ['type' => 'text', 'description' => 'IMAP Host'],
                'ticket_mail_port' => ['type' => 'integer', 'description' => 'IMAP Port', 'default' => 993],
                'ticket_mail_username' => ['type' => 'text', 'description' => 'IMAP Username'],
                'ticket_mail_password' => ['type' => 'text', 'description' => 'IMAP Password', 'encrypted' => true],
            ],
            'invoices' => [
                'invoice_prefix' => ['type' => 'text', 'description' => 'Invoice number prefix', 'default' => 'INV-'],
                'invoice_number' => ['type' => 'integer', 'description' => 'Current invoice number', 'default' => 0],
                'invoice_format' => ['type' => 'text', 'description' => 'Invoice number format', 'default' => '{number}'],
                'invoice_proforma' => ['type' => 'boolean', 'description' => 'Use proforma invoices (assign number on payment)'],
                'bill_to_text' => ['type' => 'text', 'description' => 'Bill to label on invoices'],
            ],
            'credits' => [
                'credits_enabled' => ['type' => 'boolean', 'description' => 'Enable credit system'],
                'credits_minimum_deposit' => ['type' => 'float', 'description' => 'Minimum credit deposit', 'default' => 5],
                'credits_maximum_deposit' => ['type' => 'float', 'description' => 'Maximum credit deposit', 'default' => 100],
                'credits_maximum_credit' => ['type' => 'float', 'description' => 'Maximum credit balance', 'default' => 300],
                'credits_auto_use' => ['type' => 'boolean', 'description' => 'Auto-use credits on recurring invoices'],
            ],
            'cronjob' => [
                'cronjob_time' => ['type' => 'text', 'description' => 'Scheduled cron time', 'default' => '0 * * * *'],
                'invoice_reminder_days' => ['type' => 'integer', 'description' => 'Days before due to send reminder', 'default' => 7],
                'grace_days' => ['type' => 'integer', 'description' => 'Days after due before suspension', 'default' => 3],
                'terminate_days' => ['type' => 'integer', 'description' => 'Days after suspension before termination', 'default' => 14],
            ],
            'other' => [
                'default_currency' => ['type' => 'text', 'description' => 'Default currency code', 'default' => 'USD'],
                'registration_enabled' => ['type' => 'boolean', 'description' => 'Allow new user registrations'],
                'gravatar_enabled' => ['type' => 'boolean', 'description' => 'Use Gravatar for avatars'],
            ],
        ];
    }

    public static function tax(?User $user = null): ?array
    {
        if (! self::get('tax_enabled')) {
            return null;
        }

        $country = null;
        if ($user) {
            $countryProp = $user->properties()->where('key', 'country')->first();
            $country = $countryProp?->value;
        }

        $taxRate = TaxRate::where('country', $country ?? 'all')->first();

        if (! $taxRate && $country) {
            $taxRate = TaxRate::where('country', 'all')->first();
        }

        return $taxRate?->toArray();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }
}
