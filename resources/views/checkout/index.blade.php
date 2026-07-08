@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="checkoutPage()">
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
                    @foreach ($cart->items as $item)
                        <div class="glass-light rounded-xl p-4 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if ($item->product->category)
                                        <span class="text-xs font-medium text-primary-400 bg-primary-500/10 px-2 py-0.5 rounded-md">{{ $item->product->category->name }}</span>
                                    @endif
                                    @if ($item->quantity > 1)
                                        <span class="text-xs text-dark-500">x{{ $item->quantity }}</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-white text-sm">{{ $item->plan->name }}</h3>
                                <p class="text-xs text-dark-500 mt-1">{{ $item->product->name }}</p>
                                <div class="flex gap-3 mt-1 text-xs text-dark-400">
                                    <span>${{ number_format($item->formatted_price ?? 0, 2) }}</span>
                                    @if ($item->plan->type === 'recurring')
                                        <span>/{{ $item->plan->billing_unit ?? 'mo' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-bold gradient-text">${{ number_format($item->subtotal ?? 0, 2) }}</p>
                                @if ($item->quantity > 1)
                                    <p class="text-xs text-dark-500">${{ number_format($item->formatted_price ?? 0, 2) }}/ea</p>
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
                        @foreach ($paymentGateways as $gateway)
                            <label class="flex items-center p-4 glass-light rounded-xl cursor-pointer hover:border-primary-500/30 transition-all border border-transparent"
                                   :class="{ 'border-primary-500/50 bg-primary-500/5': selectedGateway === {{ $gateway->id }} }">
                                <input type="radio" name="gateway" value="{{ $gateway->id }}" x-model="selectedGateway" class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 focus:ring-primary-500 focus:ring-offset-0">
                                <div class="ml-3">
                                    <span class="font-medium text-white text-sm">{{ $gateway->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if ($creditBalance > 0)
                        <div class="glass-light rounded-xl p-4 mt-4">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="apply_credit" value="1" x-model="applyCredit" class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 rounded focus:ring-primary-500 focus:ring-offset-0">
                                    <div>
                                        <span class="text-sm font-medium text-white">Use Account Credit</span>
                                        <p class="text-xs text-dark-500">${{ number_format($creditBalance, 2) }} available</p>
                                    </div>
                                </div>
                                <span class="text-sm text-green-400" x-show="applyCredit">-${{ number_format(min($creditBalance, $total), 2) }}</span>
                            </label>
                        </div>
                    @endif

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
                    @foreach ($cart->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">{{ $item->plan->name }} @if ($item->quantity > 1) x{{ $item->quantity }} @endif</span>
                            <span class="text-white">${{ number_format($item->subtotal ?? 0, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="border-t border-white/5 pt-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">Subtotal</span>
                            <span class="text-white">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if ($discount > 0)
                            <div class="flex justify-between text-sm text-green-400 mt-1">
                                <span>Discount</span>
                                <span>-${{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
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
function checkoutPage() {
    return {
        selectedGateway: '{{ $paymentGateways->first()?->id ?? '' }}',
        applyCredit: false,
        creditBalance: {{ $creditBalance ?? 0 }},
    }
}
</script>
@endsection
