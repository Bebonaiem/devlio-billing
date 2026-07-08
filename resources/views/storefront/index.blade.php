@extends('layouts.app')
@section('title', 'Game Server Hosting')
@section('content')
<div class="relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-1/4 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 relative">
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-500/10 border border-primary-500/20 text-primary-400 text-xs font-medium mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 animate-pulse"></span>
                High-Performance Game Hosting
            </div>
            <h1 class="text-4xl sm:text-6xl font-display font-bold text-white mb-6 leading-tight">
                Run Your Game Servers<br>
                <span class="gradient-text">At Full Speed</span>
            </h1>
            <p class="text-dark-400 text-lg max-w-2xl mx-auto">
                Blazing fast game servers with instant deployment, DDoS protection, and 24/7 monitoring. Built for gamers, by gamers.
            </p>
        </div>

        @if ($products->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-3 mb-10" x-data="{ active: 'all' }">
                <button @click="active = 'all'; document.querySelectorAll('.product-card').forEach(el => el.style.display = '')" :class="active === 'all' ? 'btn-primary text-white' : 'btn-ghost text-dark-400 hover:text-white'" class="px-5 py-2 text-sm font-medium rounded-xl transition-all">
                    All Products
                </button>
                @foreach ($products as $product)
                    <button @click="active = '{{ $product->id }}'; document.querySelectorAll('.product-card').forEach(el => el.style.display = 'none'); document.querySelectorAll('.product-card[data-id={{ $product->id }}]').forEach(el => el.style.display = '')" :class="active === '{{ $product->id }}' ? 'btn-primary text-white' : 'btn-ghost text-dark-400 hover:text-white'" class="px-5 py-2 text-sm font-medium rounded-xl transition-all">
                        {{ $product->name }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($products as $product)
                <div class="product-card glass rounded-2xl overflow-hidden card-hover group animate-slide-up animate-delay-{{ $loop->index * 100 }}" data-id="{{ $product->id }}">
                    @if ($product->image)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 to-transparent"></div>
                        </div>
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-display font-bold text-white">{{ $product->name }}</h2>
                            </div>
                        </div>
                        <p class="text-dark-400 text-sm mb-5 line-clamp-2">{{ $product->description }}</p>

                        @if ($product->plans->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($product->plans as $plan)
                                    <div class="glass-light rounded-xl p-4 hover:border-primary-500/30 transition-all group/plan">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="font-semibold text-white text-sm">{{ $plan->name }}</h3>
                                                <div class="text-xs text-dark-400 mt-1 flex flex-wrap gap-2">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        {{ $plan->cpu }}% CPU
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                        {{ $plan->memory }}MB RAM
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                                        {{ $plan->disk }}MB Disk
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-lg font-bold gradient-text">${{ number_format($plan->price, 2) }}</span>
                                                <span class="text-xs text-dark-500 block">/{{ str_replace('_', '-', $plan->billing_cycle) }}</span>
                                            </div>
                                        </div>
                                        @auth
                                            <form method="POST" action="{{ route('cart.add', $plan) }}">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="block w-full text-center py-2.5 px-4 btn-primary text-white text-sm font-medium rounded-lg transition-all hover:shadow-lg hover:shadow-primary-500/25">
                                                    Add to Cart
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('register') }}" class="block w-full text-center py-2.5 px-4 bg-dark-700 hover:bg-dark-600 text-dark-300 text-sm font-medium rounded-lg transition">
                                                Register to Order
                                            </a>
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="text-lg font-medium text-dark-300 mb-1">No products available yet</h3>
                    <p class="text-dark-500 text-sm">Check back soon for our game server offerings.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-20 glass rounded-2xl p-8 sm:p-12">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-primary-500/20 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-white mb-1">Instant Deployment</h3>
                    <p class="text-dark-400 text-sm">Your server is ready in seconds after payment.</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-green-500/20 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016a11.955 11.955 0 01-2.667 1.048 11.958 11.958 0 01-2.544.51 11.99 11.99 0 01-1.827-.104A5.974 5.974 0 0112 6.804a5.974 5.974 0 01-2.128 1.036 12.05 12.05 0 01-2.542-.375A11.98 11.98 0 014.4 5.398c.004.128.006.256.006.384A6.301 6.301 0 006 11.5a6.193 6.193 0 01-1.893.434M19 11.5a6.301 6.301 0 01-1.594-5.718 11.99 11.99 0 01-2.5.51c-.851 0-1.68-.12-2.5-.36"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-white mb-1">DDoS Protection</h3>
                    <p class="text-dark-400 text-sm">Enterprise-grade DDoS protection included.</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-500/20 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-white mb-1">24/7 Support</h3>
                    <p class="text-dark-400 text-sm">Our team is here to help you around the clock.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
