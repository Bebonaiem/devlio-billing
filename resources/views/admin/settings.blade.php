@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-4xl">
    @csrf

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">General Settings</h3>
        <p class="text-dark-400 text-sm mb-5">Basic site configuration — name, currency, tax, and account policies.</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Name</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? config('app.name') }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">The name shown in browser tabs, emails, and the site header.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site URL</label>
                <input type="text" name="site_url" value="{{ $settings['site_url'] ?? url('/') }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Full URL including https:// — used in emails and payment redirects.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Description</label>
                <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Short description used for SEO and meta tags.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Default Currency</label>
                <input type="text" name="default_currency" value="{{ $settings['default_currency'] ?? 'USD' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">3-letter code like USD, EUR, GBP. Must match a currency in Currencies.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Tax Rate (%)</label>
                <input type="number" name="tax_rate" step="0.01" value="{{ $settings['tax_rate'] ?? '0' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Default tax percentage applied to all invoices (overridable per country).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Prefix for invoice numbers (e.g. INV-000001).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Invoice Due Days</label>
                <input type="number" name="invoice_due_days" value="{{ $settings['invoice_due_days'] ?? '7' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Days after invoice creation before payment is due.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Grace Days (before suspension)</label>
                <input type="number" name="grace_days" value="{{ $settings['grace_days'] ?? '3' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Days after due date before service is suspended.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Terminate Days (after suspension)</label>
                <input type="number" name="terminate_days" value="{{ $settings['terminate_days'] ?? '14' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Days after suspension before service is permanently terminated.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Affiliate Rate (%)</label>
                <input type="number" name="affiliate_rate" step="0.01" value="{{ $settings['affiliate_rate'] ?? '10' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Percentage of each payment given to the affiliate who referred the customer.</p>
            </div>
            <div class="flex items-center pt-7">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="registration_enabled" value="1" {{ ($settings['registration_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                    <span class="text-sm text-dark-300">Allow Registration</span>
                </label>
                <p class="text-xs text-dark-500 mt-1 ml-2">Uncheck to disable new user signups.</p>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">SMTP / Mail</h3>
        <p class="text-dark-400 text-sm mb-5">Email server settings for sending invoices, password resets, and notifications.</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Host</label>
                <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="smtp.gmail.com">
                <p class="text-xs text-dark-500 mt-1">Your mail server hostname.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Port</label>
                <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '587' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="587">
                <p class="text-xs text-dark-500 mt-1">Common ports: 587 (TLS), 465 (SSL), 25 (unencrypted).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Username</label>
                <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Usually the full email address you send from.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Password</label>
                <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">App password or SMTP authentication password.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">SMTP Encryption</label>
                <select name="smtp_encryption" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (recommended)</option>
                    <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                </select>
                <p class="text-xs text-dark-500 mt-1">TLS is recommended for most providers (Gmail, Outlook, etc.).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Mail From Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="noreply@example.com">
                <p class="text-xs text-dark-500 mt-1">The "From" address shown in all outgoing emails.</p>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">Pterodactyl Panel</h3>
        <p class="text-dark-400 text-sm mb-5">Connects your billing system to your game server panel for automatic provisioning.</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Panel URL</label>
                <input type="text" name="pterodactyl_url" value="{{ $settings['pterodactyl_url'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="https://panel.yourhost.com">
                <p class="text-xs text-dark-500 mt-1">Full URL to your Pterodactyl panel (include https://).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">API Key (Admin)</label>
                <input type="password" name="pterodactyl_api_key" value="{{ $settings['pterodactyl_api_key'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Generate from Admin → Application API in your Pterodactyl panel. Needs read & write.</p>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">PayPal</h3>
        <p class="text-dark-400 text-sm mb-5">Accept payments via PayPal. Get these credentials from <a href="https://developer.paypal.com/dashboard" target="_blank" class="text-primary-400 hover:underline">PayPal Developer Dashboard</a>.</p>
        <div class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Client ID</label>
                    <input type="text" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <p class="text-xs text-dark-500 mt-1">From REST API apps → your app → Client ID.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Client Secret</label>
                    <input type="password" name="paypal_secret" value="{{ $settings['paypal_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <p class="text-xs text-dark-500 mt-1">From REST API apps → your app → Secret.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Webhook ID</label>
                    <input type="text" name="paypal_webhook_id" value="{{ $settings['paypal_webhook_id'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <p class="text-xs text-dark-500 mt-1">From Developer Dashboard → Webhooks → Add webhook → URL: <code class="text-primary-400">{{ url('/webhook/paypal') }}</code></p>
                </div>
                <div class="flex items-center pt-7">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="paypal_sandbox" value="1" {{ ($settings['paypal_sandbox'] ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                        <span class="text-sm text-dark-300">Sandbox Mode (testing)</span>
                    </label>
                </div>
            </div>
            <div class="p-4 glass-light rounded-xl text-sm text-dark-400 space-y-1">
                <p><span class="text-white font-medium">Setup Steps:</span></p>
                <p>1. Go to <a href="https://developer.paypal.com/dashboard" target="_blank" class="text-primary-400 hover:underline">PayPal Developer Dashboard</a></p>
                <p>2. Create a REST API app to get Client ID &amp; Secret</p>
                <p>3. Create a Webhook pointing to: <code class="text-primary-400">{{ url('/webhook/paypal') }}</code></p>
                <p>4. Webhook events: <code>CHECKOUT.ORDER.APPROVED</code> and <code>PAYMENT.CAPTURE.COMPLETED</code></p>
                <p>5. Copy the Webhook ID and paste above</p>
                <p>6. Uncheck "Sandbox Mode" when going live</p>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">Stripe</h3>
        <p class="text-dark-400 text-sm mb-5">Accept payments via Stripe. Get these credentials from <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="text-primary-400 hover:underline">Stripe Dashboard</a>.</p>
        <div class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Publishable Key</label>
                    <input type="text" name="stripe_key" value="{{ $settings['stripe_key'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="pk_live_... or pk_test_...">
                    <p class="text-xs text-dark-500 mt-1">Starts with <code>pk_live_</code> (live) or <code>pk_test_</code> (test).</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Secret Key</label>
                    <input type="password" name="stripe_secret" value="{{ $settings['stripe_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="sk_live_... or sk_test_...">
                    <p class="text-xs text-dark-500 mt-1">Starts with <code>sk_live_</code> (live) or <code>sk_test_</code> (test).</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-2">Webhook Secret</label>
                    <input type="password" name="stripe_webhook_secret" value="{{ $settings['stripe_webhook_secret'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="whsec_...">
                    <p class="text-xs text-dark-500 mt-1">Starts with <code>whsec_</code>. Created when you add a webhook endpoint.</p>
                </div>
            </div>
            <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4 text-sm text-dark-300 space-y-1">
                <p><span class="text-yellow-400 font-semibold">Important:</span> Webhook secret is NOT the same as your secret key.</p>
                <p>You must create a webhook endpoint in Stripe Dashboard at:</p>
                <p class="text-white font-mono text-center py-2">{{ url('/webhook/stripe') }}</p>
                <p>Select events: <code>checkout.session.completed</code>, <code>invoice.paid</code>, <code>invoice.payment_failed</code></p>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-display font-bold text-white mb-1">Email Piping</h3>
        <p class="text-dark-400 text-sm mb-5">IMAP settings for receiving ticket replies via email. Run <code class="text-primary-400">php artisan app:fetch-emails</code> to poll for new messages.</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">IMAP Host</label>
                <input type="text" name="email_host" value="{{ $settings['email_host'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="imap.gmail.com">
                <p class="text-xs text-dark-500 mt-1">Your IMAP server hostname.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">IMAP Port</label>
                <input type="text" name="email_port" value="{{ $settings['email_port'] ?? '993' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="993">
                <p class="text-xs text-dark-500 mt-1">Common ports: 993 (SSL), 143 (TLS).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Email Address</label>
                <input type="email" name="email_address" value="{{ $settings['email_address'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" placeholder="support@yourdomain.com">
                <p class="text-xs text-dark-500 mt-1">The mailbox to monitor for incoming ticket replies.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Email Password</label>
                <input type="password" name="email_password" value="{{ $settings['email_password'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <p class="text-xs text-dark-500 mt-1">Use an app password for providers like Gmail.</p>
            </div>
        </div>
    </div>

    <button type="submit" class="w-full py-3.5 px-4 btn-primary text-white font-semibold rounded-xl text-sm hover:shadow-lg hover:shadow-primary-500/25 transition-all">Save All Settings</button>
</form>
@endsection