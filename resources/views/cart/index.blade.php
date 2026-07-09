@extends('layouts.dashboard')
@section('title', 'Shopping Cart')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-display font-bold text-white">Shopping Cart</h1>
            <p class="text-dark-400 mt-1">{{ $items->count() }} item(s) in your cart</p>
        </div>
        @if($items->isNotEmpty())
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Clear Cart</button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if ($items->isEmpty())
        <div class="glass rounded-2xl p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-2">Your cart is empty</h3>
            <p class="text-dark-400 mb-6">Browse our products and add something awesome!</p>
            <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">
                Browse Products
            </a>
        </div>
    @else
        <div class="space-y-4 mb-8">
            @foreach ($items as $item)
                @php
                    $price = $item->formatted_price ?? 0;
                    $setupFee = $item->formatted_setup_fee ?? 0;
                @endphp
                <div class="glass rounded-2xl p-6 animate-slide-up">
                    <div class="flex flex-col sm:flex-row gap-5">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    @if ($item->product->category)
                                        <span class="text-xs font-medium text-primary-400 bg-primary-500/10 px-2 py-0.5 rounded-md">{{ $item->product->category->name }}</span>
                                    @endif
                                    <h3 class="text-lg font-display font-bold text-white mt-2">{{ $item->plan->name }}</h3>
                                    <p class="text-xs text-dark-500">{{ $item->product->name }}</p>
                                </div>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-dark-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>

                            <div class="flex flex-wrap gap-3 mb-4 text-xs text-dark-400">
                                <span class="text-dark-300 font-medium">${{ number_format($price, 2) }}</span>
                                @if ($item->plan->type === 'recurring')
                                    <span>/{{ $item->plan->billing_unit ?? 'mo' }}</span>
                                @endif
                                @if ($setupFee > 0)
                                    <span>+ ${{ number_format($setupFee, 2) }} setup fee</span>
                                @endif
                            </div>

                            @if ($item->config_options && is_array($item->config_options) && count($item->config_options) > 0)
                                <div class="glass-light rounded-lg p-3 mb-4 text-xs space-y-1">
                                    @foreach ($item->config_options as $configId => $configValue)
                                        <p class="text-dark-400">Config Option #{{ $configId }}: <span class="text-dark-200">{{ $configValue }}</span></p>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="sm:w-48 flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3">
                            <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item->quantity }}">
                                <div class="flex items-center glass rounded-lg overflow-hidden">
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="px-3 py-2 text-dark-400 hover:text-white hover:bg-white/5 transition text-sm">-</button>
                                    <span class="px-3 py-2 text-sm text-white font-medium min-w-[2rem] text-center">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ min(10, $item->quantity + 1) }}" class="px-3 py-2 text-dark-400 hover:text-white hover:bg-white/5 transition text-sm">+</button>
                                </div>
                            </form>
                            <div class="text-right">
                                <p class="text-xl font-bold gradient-text">${{ number_format($item->subtotal ?? 0, 2) }}</p>
                                @if ($item->quantity > 1)
                                    <p class="text-xs text-dark-500">${{ number_format($price, 2) }} each</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="glass rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="font-display font-bold text-white">Order Summary</h3>
            </div>

            @if ($cart->coupon)
                <div class="glass-light rounded-xl p-3 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span class="text-sm text-white">Coupon: {{ $cart->coupon->code }}</span>
                    </div>
                    <form method="POST" action="{{ route('cart.coupon.remove') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300">Remove</button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="code" placeholder="Coupon code" class="flex-1 px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                    <button type="submit" class="px-4 py-2.5 btn-ghost text-dark-300 hover:text-white text-sm font-medium rounded-xl">Apply</button>
                </form>
            @endif

            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-dark-400">{{ $item->product->name }} - {{ $item->plan->name }} x{{ $item->quantity }}</span>
                        <span class="text-white">${{ number_format($item->subtotal ?? 0, 2) }}</span>
                    </div>
                @endforeach
                @if ($discount > 0)
                    <div class="flex justify-between text-sm text-green-400">
                        <span>Discount ({{ $cart->coupon->code }})</span>
                        <span>-${{ number_format($discount, 2) }}</span>
                    </div>
                @endif
                <div class="border-t border-white/5 pt-3 flex justify-between">
                    <span class="font-bold text-white">Total</span>
                    <span class="text-xl font-bold gradient-text">${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('storefront') }}" class="flex-1 text-center py-3 px-6 btn-ghost text-dark-300 hover:text-white font-medium rounded-xl text-sm">
                Continue Shopping
            </a>
            <a href="{{ route('checkout.index') }}" class="flex-1 text-center py-3 px-6 btn-primary text-white font-semibold rounded-xl text-sm">
                Proceed to Checkout
            </a>
        </div>
    @endif
</div>
@endsection
