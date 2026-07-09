<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Shopping Cart</h1>
            <p class="text-dark-400 mt-1">Review your items before checkout</p>
        </div>

        @if(!$cart || $cart->items->isEmpty())
            <div class="glass rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <p class="text-dark-400">Your cart is empty.</p>
                <a href="{{ route('storefront') }}" class="mt-4 inline-block px-4 py-2 btn-primary text-white rounded-lg text-sm">Browse Store</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart->items as $item)
                        <div class="glass rounded-xl p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-500/20 to-purple-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-white">{{ $item->product->name ?? 'Product' }}</h3>
                                        <p class="text-sm text-dark-400">{{ $item->plan->name ?? 'Plan' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white hover:bg-white/10">-</button>
                                        <span class="text-sm text-white w-8 text-center">{{ $item->quantity }}</span>
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white hover:bg-white/10">+</button>
                                    </div>
                                    <p class="text-sm font-medium text-white w-24 text-right">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    <button wire:click="removeItem({{ $item->id }})" class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex justify-end">
                        <button wire:click="clearCart" class="px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg">Clear Cart</button>
                    </div>
                </div>

                <div class="glass rounded-xl p-6 h-fit">
                    <h2 class="text-lg font-semibold text-white mb-4">Order Summary</h2>
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

                    <div class="mt-6 space-y-3">
                        <div class="flex gap-2">
                            <input type="text" wire:model="couponCode" placeholder="Coupon code" class="flex-1 px-4 py-2.5 rounded-lg input-field text-white text-sm placeholder-dark-500">
                            <button wire:click="applyCoupon" class="px-4 py-2.5 btn-ghost text-white rounded-lg text-sm">Apply</button>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="block w-full px-4 py-2.5 btn-primary text-white rounded-lg text-sm font-medium text-center">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
