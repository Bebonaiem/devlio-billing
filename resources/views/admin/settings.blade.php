@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-3xl">
    @csrf
    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-5">General</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Name</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? config('app.name') }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site URL</label>
                <input type="text" name="site_url" value="{{ $settings['site_url'] ?? url('/') }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Description</label>
                <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Default Currency</label>
                <input type="text" name="default_currency" value="{{ $settings['default_currency'] ?? 'USD' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Tax Rate (%)</label>
                <input type="number" name="tax_rate" step="0.01" value="{{ $settings['tax_rate'] ?? '0' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Invoice Due Days</label>
                <input type="number" name="invoice_due_days" value="{{ $settings['invoice_due_days'] ?? '7' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Grace Days (before suspension)</label>
                <input type="number" name="grace_days" value="{{ $settings['grace_days'] ?? '3' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Terminate Days (after suspension)</label>
                <input type="number" name="terminate_days" value="{{ $settings['terminate_days'] ?? '14' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Affiliate Rate (%)</label>
                <input type="number" name="affiliate_rate" step="0.01" value="{{ $settings['affiliate_rate'] ?? '10' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div class="flex items-center pt-7">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="registration_enabled" value="1" {{ ($settings['registration_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                    <span class="text-sm text-dark-300">Allow Registration</span>
                </label>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-5">SMTP / Mail</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Host</label>
                <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Port</label>
                <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '587' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Username</label>
                <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Password</label>
                <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Encryption</label>
                <select name="smtp_encryption" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Mail From Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-5">Pterodactyl</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Panel URL</label>
                <input type="text" name="pterodactyl_url" value="{{ $settings['pterodactyl_url'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="https://panel.example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">API Key (Admin)</label>
                <input type="password" name="pterodactyl_api_key" value="{{ $settings['pterodactyl_api_key'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-5">PayPal</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Client ID</label>
                <input type="text" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Client Secret</label>
                <input type="password" name="paypal_secret" value="{{ $settings['paypal_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Webhook ID</label>
                <input type="text" name="paypal_webhook_id" value="{{ $settings['paypal_webhook_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div class="flex items-center pt-7">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="paypal_sandbox" value="1" {{ ($settings['paypal_sandbox'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                    <span class="text-sm text-dark-300">Sandbox Mode</span>
                </label>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-5">Stripe</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Publishable Key</label>
                <input type="text" name="stripe_key" value="{{ $settings['stripe_key'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Secret Key</label>
                <input type="password" name="stripe_secret" value="{{ $settings['stripe_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Webhook Secret</label>
                <input type="password" name="stripe_webhook_secret" value="{{ $settings['stripe_webhook_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
    </div>

    <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Save All Settings</button>
</form>
@endsection