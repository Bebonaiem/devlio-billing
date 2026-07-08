@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Name</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? config('app.name') }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Site Description</label>
                <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Currency</label>
                <select name="currency" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP</option>
                </select>
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
                <label class="block text-sm font-medium text-dark-300 mb-2">Affiliate Rate (%)</label>
                <input type="number" name="affiliate_rate" step="0.01" value="{{ $settings['affiliate_rate'] ?? '10' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Grace Days (before suspension)</label>
                <input type="number" name="grace_days" value="{{ $settings['grace_days'] ?? '3' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Terminate Days (before deletion)</label>
                <input type="number" name="terminate_days" value="{{ $settings['terminate_days'] ?? '14' }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Save Settings</button>
    </form>
</div>
@endsection