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

        @if ($categories->isNotEmpty())
            <div class="mb-10">
                <h2 class="text-2xl font-display font-bold text-white mb-6 text-center">Browse by Category</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('storefront.category', $category->slug) }}" class="glass rounded-xl p-5 group hover:border-primary-500/30 transition-all animate-slide-up">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center group-hover:bg-primary-500/30 transition">
                                    @if ($category->image)
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-8 h-8 rounded-lg object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-white group-hover:text-primary-300 transition">{{ $category->name }}</h3>
                                    <p class="text-dark-400 text-sm">{{ $category->products->count() }} products</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($products->isNotEmpty())
            <div class="mb-10">
                <h2 class="text-2xl font-display font-bold text-white mb-6 text-center">All Products</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="glass rounded-2xl overflow-hidden card-hover group animate-slide-up">
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
                                        <h3 class="text-lg font-display font-bold text-white">{{ $product->name }}</h3>
                                        @if ($product->category)
                                            <span class="text-xs text-dark-500">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-dark-400 text-sm mb-5 line-clamp-2">{{ $product->description }}</p>

                                @if ($product->plans->isNotEmpty())
                                    <div class="space-y-3">
                                        @foreach ($product->plans->take(3) as $plan)
                                            @php
                                                $price = $plan->prices->first();
                                            @endphp
                                            <div class="glass-light rounded-xl p-4 hover:border-primary-500/30 transition-all">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h4 class="font-semibold text-white text-sm">{{ $plan->name }}</h4>
                                                        <span class="text-xs text-dark-500">{{ ucfirst(str_replace('-', ' ', $plan->type)) }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        @if ($plan->type === 'free')
                                                            <span class="text-lg font-bold text-green-400">Free</span>
                                                        @elseif ($price)
                                                            <span class="text-lg font-bold gradient-text">${{ number_format($price->price, 2) }}</span>
                                                            @if ($plan->type === 'recurring')
                                                                <span class="text-xs text-dark-500 block">/{{ $plan->billing_unit ?? 'mo' }}</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a href="{{ route('storefront.product', $product->slug) }}" class="block w-full text-center py-2.5 px-4 btn-primary text-white text-sm font-medium rounded-lg transition-all hover:shadow-lg hover:shadow-primary-500/25 mt-4">
                                        View Plans
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-lg font-medium text-dark-300 mb-1">No products available yet</h3>
                <p class="text-dark-500 text-sm">Check back soon for our game server offerings.</p>
            </div>
        @endif

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
