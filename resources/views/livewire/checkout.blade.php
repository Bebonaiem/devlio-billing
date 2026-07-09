<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Checkout</h1>
            <p class="text-dark-400 mt-1">Complete your order</p>
        </div>

        @if(!$cart || $cart->items->isEmpty())
            <div class="glass rounded-xl p-12 text-center">
                <p class="text-dark-400">Your cart is empty.</p>
                <a href="{{ route('storefront') }}" class="mt-4 inline-block px-4 py-2 btn-primary text-white rounded-lg text-sm">Browse Store</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="glass rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Order Items</h2>
                        <div class="space-y-3">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $item->product->name ?? 'Product' }}</p>
                                        <p class="text-xs text-dark-400">{{ $item->plan->name ?? 'Plan' }} x {{ $item->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-white">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="glass rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Payment Method</h2>
                        @if($gatewayExtension)
                            <p class="text-sm text-dark-400">Payment will be processed via {{ $gatewayExtension->name }}</p>
                        @else
                            <p class="text-sm text-dark-400">No payment gateway configured.</p>
                        @endif
                    </div>

                    <div class="glass rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">Currency</h2>
                        <div class="flex gap-2">
                            @foreach($currencies as $currency)
                                <button wire:click="setCurrency('{{ $currency->code }}')" class="px-4 py-2 rounded-lg text-sm {{ $currencyCode === $currency->code ? 'btn-primary text-white' : 'btn-ghost text-dark-300 hover:text-white' }}">
                                    {{ $currency->code }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="glass rounded-xl p-6 h-fit">
                    <h2 class="text-lg font-semibold text-white mb-4">Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">Subtotal</span>
                            <span class="text-white">${{ number_format($cart->items->sum(fn ($item) => $item->price * $item->quantity), 2) }}</span>
                        </div>
                        @if($cart->coupon)
                            <div class="flex justify-between text-sm">
                                <span class="text-green-400">Coupon ({{ $cart->coupon->code }})</span>
                                <span class="text-green-400">-${{ number_format($cart->coupon->calculateDiscount($cart->items->sum(fn ($item) => $item->price * $item->quantity)), 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-white/5 pt-3 flex justify-between">
                            <span class="text-white font-medium">Total</span>
                            <span class="text-white font-bold">${{ number_format($cart->items->sum(fn ($item) => $item->price * $item->quantity), 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        @if($cart->coupon)
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-sm text-green-400">Coupon: {{ $cart->coupon->code }}</span>
                                <button wire:click="removeCoupon" class="text-red-400 hover:text-red-300 text-xs">Remove</button>
                            </div>
                        @else
                            <div class="flex gap-2 mb-3">
                                <input type="text" wire:model="couponCode" placeholder="Coupon code" class="flex-1 px-4 py-2.5 rounded-lg input-field text-white text-sm placeholder-dark-500">
                                <button wire:click="applyCoupon" class="px-4 py-2.5 btn-ghost text-white rounded-lg text-sm">Apply</button>
                            </div>
                        @endif

                        <button wire:click="process" class="block w-full px-4 py-2.5 btn-primary text-white rounded-lg text-sm font-medium text-center">Complete Order</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
