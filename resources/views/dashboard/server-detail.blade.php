@extends('layouts.dashboard')
@section('title', $service->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard.servers') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Services
        </a>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-white mb-1">{{ $service->name }}</h1>
                <p class="text-dark-400 text-sm">{{ $service->product->name ?? 'N/A' }} &mdash; {{ $service->plan->name ?? 'N/A' }}</p>
            </div>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                {{ $service->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                {{ $service->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                {{ $service->status === 'cancelled' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}">
                <span class="w-2 h-2 rounded-full {{ $service->status === 'active' ? 'bg-green-400' : ($service->status === 'suspended' ? 'bg-red-400' : 'bg-yellow-400') }}"></span>
                {{ ucfirst($service->status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Product</dt><dd class="text-white text-sm">{{ $service->product->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Plan</dt><dd class="text-white text-sm">{{ $service->plan->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Billing</dt><dd class="text-white text-sm capitalize">{{ $service->plan->billing_period ? $service->plan->billing_period . ' ' . $service->plan->billing_unit : 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Price</dt><dd class="text-white text-sm">${{ number_format($service->plan->prices->first()?->price ?? 0, 2) }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Node</dt><dd class="text-white text-sm">{{ $service->node ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Created</dt><dd class="text-white text-sm">{{ $service->created_at->format('M d, Y') }}</dd></div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Config Options</h3>
                @if ($service->configs->isNotEmpty())
                    <dl class="space-y-3">
                        @foreach ($service->configs as $config)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <dt class="text-dark-400 text-sm">{{ $config->option->name ?? 'N/A' }}</dt>
                                <dd class="text-white text-sm">{{ $config->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="text-sm text-dark-500">No config options set.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
