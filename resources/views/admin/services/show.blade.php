@extends('layouts.admin')
@section('title', 'Service #' . $service->id)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.services.index') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Services
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Service #{{ $service->id }}</h1>
                <p class="text-dark-400 mt-1">{{ $service->user->name }} - {{ $service->product->name ?? 'N/A' }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium
                {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($service->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600') }}">
                {{ ucfirst($service->status) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6">
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-display font-bold text-white mb-4">Service Details</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-dark-500 mb-1 block">User</label>
                        <p class="text-white font-medium">{{ $service->user->name }}</p>
                        <p class="text-xs text-dark-500">{{ $service->user->email }}</p>
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
                        <p class="text-white font-medium">{{ $service->created_at->format('M j, Y g:i A') }}</p>
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
                                <span class="text-dark-400 text-sm">{{ $config->configOption?->name ?? 'N/A' }}</span>
                                <span class="text-white text-sm font-medium">{{ $config->configValue->name ?? $config->config_value_id }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($service->invoices->isNotEmpty())
                <div class="glass rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5">
                        <h2 class="text-lg font-display font-bold text-white">Invoices</h2>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach ($service->invoices as $invoice)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-white">{{ $invoice->number }}</p>
                                    <p class="text-xs text-dark-500">{{ $invoice->created_at->format('M j, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-primary-400 hover:text-primary-300 text-xs">View</a>
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
                    @if ($service->status === 'active')
                        <form method="POST" action="{{ route('admin.services.suspend', $service) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-400 text-sm font-medium transition-all text-left flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Suspend Service
                            </button>
                        </form>
                    @endif
                    @if ($service->status === 'suspended')
                        <form method="POST" action="{{ route('admin.services.unsuspend', $service) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-green-500/10 hover:bg-green-500/20 text-green-400 text-sm font-medium transition-all text-left flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Unsuspend Service
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.services.terminate', $service) }}" onsubmit="return confirm('Are you sure you want to terminate this service?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-medium transition-all text-left flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Terminate Service
                        </button>
                    </form>
                </div>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-display font-bold text-white mb-4">Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-dark-400">Service ID</span>
                        <span class="text-white">#{{ $service->id }}</span>
                    </div>
                    @if ($service->currency)
                        <div class="flex justify-between">
                            <span class="text-dark-400">Currency</span>
                            <span class="text-white">{{ $service->currency_code }}</span>
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
