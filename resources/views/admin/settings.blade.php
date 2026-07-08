@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Site Name</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? config('app.name') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Site Description</label>
                <input type="text" name="site_description" value="{{ $settings['site_description'] ?? '' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency</label>
                <select name="currency" class="w-full border rounded px-3 py-2">
                    <option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tax Rate (%)</label>
                <input type="number" name="tax_rate" step="0.01" value="{{ $settings['tax_rate'] ?? '0' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Affiliate Rate (%)</label>
                <input type="number" name="affiliate_rate" step="0.01" value="{{ $settings['affiliate_rate'] ?? '10' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Grace Days (before suspension)</label>
                <input type="number" name="grace_days" value="{{ $settings['grace_days'] ?? '3' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Terminate Days (before deletion)</label>
                <input type="number" name="terminate_days" value="{{ $settings['terminate_days'] ?? '14' }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save Settings</button>
    </form>
</div>
@endsection
