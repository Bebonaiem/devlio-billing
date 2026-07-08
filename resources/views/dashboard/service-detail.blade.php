@extends('layouts.dashboard')
@section('title', $service->label ?? 'Service Detail')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('dashboard.services') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Services
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">{{ $service->label ?? $service->product->name ?? 'Service' }}</h1>
                <p class="text-dark-400 mt-1">{{ $service->product->name ?? '' }} - {{ $service->plan->name ?? '' }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium
                {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($service->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600') }}">
                {{ ucfirst($service->status) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-display font-bold text-white mb-4">Service Details</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">Status</label>
                        <p class="text-white font-medium">{{ ucfirst($service->status) }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">Product</label>
                        <p class="text-white font-medium">{{ $service->product->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">Plan</label>
                        <p class="text-white font-medium">{{ $service->plan->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">Price</label>
                        <p class="text-white font-medium">${{ number_format($service->price, 2) }}/{{ $service->plan->billing_unit ?? 'mo' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">Created</label>
                        <p class="text-white font-medium">{{ $service->created_at->format('M j, Y') }}</p>
                    </div>
                    @if ($service->expires_at)
                        <div>
                            <label class="text-xs text-dark-500 mb-1 block">Expires</label>
                            <p class="text-white font-medium">{{ $service->expires_at->format('M j, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($service->configs->isNotEmpty())
                <div class="glass rounded-2xl p-6">
                    <h2 class="text-lg font-display font-bold text-white mb-4">Configuration</h2>
                    <div class="space-y-3">
                        @foreach ($service->configs as $config)
                            <div class="glass-light rounded-xl p-3 flex justify-between">
                                <span class="text-dark-400 text-sm">{{ $config->configOption->name ?? 'Config' }}</span>
                                <span class="text-white text-sm font-medium">{{ $config->configValue->name ?? $config->config_value_id }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($invoices->isNotEmpty())
                <div class="glass rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5">
                        <h2 class="text-lg font-display font-bold text-white">Recent Invoices</h2>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach ($invoices as $invoice)
                            <div class="px-6 py-4 hover:bg-white/[0.02] transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-white font-medium">{{ $invoice->number }}</p>
                                        <p class="text-xs text-dark-500">{{ $invoice->created_at->format('M j, Y') }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                            {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-primary-400 hover:text-primary-300 text-xs">View</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-display font-bold text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('storefront') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary-500/10 hover:bg-primary-500/20 transition-all text-primary-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Renew Service
                    </a>
                </div>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-display font-bold text-white mb-4">Service Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-dark-400">Service ID</span>
                        <span class="text-white">#{{ $service->id }}</span>
                    </div>
                    @if ($service->currency)
                        <div class="flex justify-between">
                            <span class="text-dark-400">Currency</span>
                            <span class="text-white">{{ $service->currency->code }}</span>
                        </div>
                    @endif
                    @if ($service->coupon)
                        <div class="flex justify-between">
                            <span class="text-dark-400">Coupon</span>
                            <span class="text-green-400">{{ $service->coupon->code }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
