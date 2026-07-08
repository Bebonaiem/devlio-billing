@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('storefront') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Products
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        @if ($product->image)
            <div class="relative h-64 sm:h-80 overflow-hidden">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-dark-950/90 via-dark-950/40 to-transparent"></div>
                <div class="absolute bottom-6 left-8">
                    <h1 class="text-3xl sm:text-4xl font-display font-bold text-white">{{ $product->name }}</h1>
                </div>
            </div>
        @endif
        <div class="p-8">
            @unless ($product->image)
                <h1 class="text-3xl font-display font-bold text-white mb-3">{{ $product->name }}</h1>
            @endunless
            <p class="text-dark-400 text-lg mb-8">{{ $product->description }}</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($product->plans as $plan)
                    <div class="glass rounded-xl p-6 hover:border-primary-500/30 transition-all group">
                        <h3 class="text-xl font-display font-bold text-white mb-4">{{ $plan->name }}</h3>

                        <div class="mb-6">
                            <span class="text-3xl font-bold gradient-text">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-dark-500 text-sm">/{{ str_replace('_', '-', $plan->billing_cycle) }}</span>
                            @if ($plan->setup_fee > 0)
                                <p class="text-xs text-dark-500 mt-1">+ ${{ number_format($plan->setup_fee, 2) }} setup fee</p>
                            @endif
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->cpu }}% CPU
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->memory }} MB RAM
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->disk }} MB Disk
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->swap }} MB Swap
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->databases }} Databases
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-dark-300">
                                <div class="w-5 h-5 rounded-full bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{ $plan->backups }} Backups
                            </li>
                        </ul>

                        @auth
                            <a href="{{ route('checkout.index', $plan) }}" class="block w-full text-center py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">
                                Order Now
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="block w-full text-center py-3 px-4 bg-dark-700 hover:bg-dark-600 text-dark-300 font-medium rounded-xl text-sm transition">
                                Register to Order
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection