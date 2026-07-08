@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="text-2xl font-display font-bold text-white">Checkout</h1>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            <div class="glass rounded-2xl p-6">
                <h2 class="font-display font-bold text-white mb-4">Order Summary</h2>
                <div class="space-y-3">
                    @foreach ($items as $key => $item)
                        <div class="glass-light rounded-xl p-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium text-primary-400 bg-primary-500/10 px-2 py-0.5 rounded-md">{{ $item['plan']->product->name }}</span>
                                    @if ($item['quantity'] > 1)
                                        <span class="text-xs text-dark-500">x{{ $item['quantity'] }}</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-white text-sm">{{ $item['plan']->name }}</h3>
                                <div class="flex gap-3 mt-1 text-xs text-dark-400">
                                    <span>{{ $item['plan']->cpu }}% CPU</span>
                                    <span>{{ $item['plan']->memory }}MB RAM</span>
                                    <span>{{ $item['plan']->disk }}MB Disk</span>
                                </div>
                                @if (!empty($item['config']['hostname']) || !empty($item['config']['game_username']))
                                    <div class="mt-2 text-xs text-dark-500">
                                        @if (!empty($item['config']['hostname']))
                                            Hostname: {{ $item['config']['hostname'] }}
                                        @endif
                                        @if (!empty($item['config']['game_username']))
                                            | Username: {{ $item['config']['game_username'] }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-bold gradient-text">${{ number_format($item['subtotal'], 2) }}</p>
                                @if ($item['quantity'] > 1)
                                    <p class="text-xs text-dark-500">${{ number_format($item['plan']->price, 2) }}/ea</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass rounded-2xl p-6">
                <h2 class="font-display font-bold text-white mb-4">Payment Method</h2>
                <form method="POST" action="{{ route('checkout.process') }}" id="paymentForm" class="space-y-4">
                    @csrf
                    <div class="space-y-3">
                        <label class="flex items-center p-4 glass-light rounded-xl cursor-pointer hover:border-primary-500/30 transition-all border border-transparent" :class="{ 'border-primary-500/50 bg-primary-500/5': gateway === 'stripe' }">
                            <input type="radio" name="gateway" value="stripe" x-model="gateway" class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 focus:ring-primary-500 focus:ring-offset-0">
                            <div class="ml-3 flex items-center gap-3">
                                <div class="w-10 h-7 rounded bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-7.076-2.19L3.36 21.8C5.578 22.926 8.758 24 12.21 24c2.631 0 4.808-.657 6.298-1.878 1.676-1.36 2.519-3.397 2.519-5.937 0-4.114-2.553-5.843-7.051-7.035z"/></svg>
                                </div>
                                <div>
                                    <span class="font-medium text-white text-sm">Credit / Debit Card</span>
                                    <p class="text-xs text-dark-500">Powered by Stripe</p>
                                </div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 glass-light rounded-xl cursor-pointer hover:border-primary-500/30 transition-all border border-transparent" :class="{ 'border-primary-500/50 bg-primary-500/5': gateway === 'paypal' }">
                            <input type="radio" name="gateway" value="paypal" x-model="gateway" class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 focus:ring-primary-500 focus:ring-offset-0">
                            <div class="ml-3 flex items-center gap-3">
                                <div class="w-10 h-7 rounded bg-gradient-to-r from-blue-600 to-blue-400 flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">PayPal</span>
                                </div>
                                <div>
                                    <span class="font-medium text-white text-sm">PayPal</span>
                                    <p class="text-xs text-dark-500">Pay with your PayPal account</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="border-t border-white/5 pt-4 mt-4">
                        <button type="submit" class="w-full py-3.5 px-4 btn-primary text-white font-semibold rounded-xl text-sm hover:shadow-lg hover:shadow-primary-500/25 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Pay ${{ number_format($total, 2) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="glass rounded-2xl p-6 sticky top-24">
                <h3 class="font-display font-bold text-white mb-4">Price Breakdown</h3>
                <div class="space-y-3">
                    @foreach ($items as $key => $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">{{ $item['plan']->name }} @if ($item['quantity'] > 1) x{{ $item['quantity'] }} @endif</span>
                            <span class="text-white">${{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="border-t border-white/5 pt-3">
                        <div class="flex justify-between">
                            <span class="text-dark-400">Subtotal</span>
                            <span class="text-white">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2">
                        <span class="text-white">Total</span>
                        <span class="gradient-text">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 p-4 glass-light rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016a11.955 11.955 0 01-2.667 1.048 11.958 11.958 0 01-2.544.51 11.99 11.99 0 01-1.827-.104A5.974 5.974 0 0112 6.804a5.974 5.974 0 01-2.128 1.036 12.05 12.05 0 01-2.542-.375A11.98 11.98 0 014.4 5.398c.004.128.006.256.006.384A6.301 6.301 0 006 11.5a6.193 6.193 0 01-1.893.434M19 11.5a6.301 6.301 0 01-1.594-5.718 11.99 11.99 0 01-2.5.51c-.851 0-1.68-.12-2.5-.36"/></svg>
                        <span class="text-sm font-medium text-white">Instant Deployment</span>
                    </div>
                    <p class="text-xs text-dark-400">Your server will be ready immediately after payment.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function productPage() {
    return {
        gateway: 'stripe'
    }
}
</script>
@endsection
