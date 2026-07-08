@extends('layouts.dashboard')
@section('title', 'My Services')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-display font-bold text-white">My Services</h1>
            <p class="text-dark-400 mt-1">Manage your active services and subscriptions.</p>
        </div>
        <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-4 py-2 btn-primary text-white text-sm font-medium rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Order
        </a>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if ($services->isEmpty())
        <div class="glass rounded-2xl p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-2">No services yet</h3>
            <p class="text-dark-400 mb-6">Browse our products and order your first service!</p>
            <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-4">
            @foreach ($services as $service)
                <a href="{{ route('dashboard.service-detail', $service) }}" class="glass rounded-2xl p-6 card-hover group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center group-hover:bg-primary-500/30 transition">
                                <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-white group-hover:text-primary-300 transition">{{ $service->label ?? $service->product->name ?? 'Service' }}</h3>
                                <p class="text-xs text-dark-500">{{ $service->product->name ?? 'Unknown Product' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                            {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($service->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600') }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs text-dark-400 mb-4">
                        @if ($service->plan)
                            <span class="glass-light px-2 py-1 rounded">{{ $service->plan->name }}</span>
                        @endif
                        @if ($service->currency)
                            <span class="glass-light px-2 py-1 rounded">${{ number_format($service->price, 2) }}/{{ $service->plan->billing_unit ?? 'mo' }}</span>
                        @endif
                    </div>
                    @if ($service->expires_at)
                        <p class="text-xs text-dark-500">Expires: {{ $service->expires_at->format('M j, Y') }}</p>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
