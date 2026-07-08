@extends('layouts.app')
@section('title', 'Game Server Hosting')
@section('content')
<div class="relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-1/4 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 relative">
        <div class="text-center mb-16 animate-fade-in">
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

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($products as $product)
                <div class="glass rounded-2xl overflow-hidden card-hover group animate-slide-up animate-delay-{{ $loop->index * 100 }}">
                    @if ($product->image)
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950/80 to-transparent"></div>
                        </div>
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-display font-bold text-white mb-2">{{ $product->name }}</h2>
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
                                            <a href="{{ route('checkout.index', $plan) }}" class="block w-full text-center py-2.5 px-4 btn-primary text-white text-sm font-medium rounded-lg">
                                                Order Now
                                            </a>
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
    </div>
</div>
@endsection