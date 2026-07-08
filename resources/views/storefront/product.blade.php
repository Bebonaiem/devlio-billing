@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="productPage()">
    <div class="mb-8">
        <a href="{{ route('storefront') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Products
        </a>
    </div>

    <div class="glass rounded-2xl overflow-hidden mb-8">
        @if ($product->image)
            <div class="relative h-48 sm:h-64 overflow-hidden">
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
            <p class="text-dark-400 text-lg">{{ $product->description }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            @foreach ($product->plans as $plan)
                <div class="glass rounded-2xl p-6 transition-all hover:border-primary-500/30"
                     :class="{ 'border border-primary-500/50 shadow-lg shadow-primary-500/10': selectedPlan === {{ $plan->id }} }">
                    <div class="flex flex-col sm:flex-row gap-5">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-xl font-display font-bold text-white">{{ $plan->name }}</h3>
                                    @if ($plan->setup_fee > 0)
                                        <p class="text-xs text-dark-500 mt-1">+ ${{ number_format($plan->setup_fee, 2) }} setup fee</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold gradient-text">${{ number_format($plan->price, 2) }}</span>
                                    <span class="text-dark-500 text-sm">/{{ str_replace('_', '-', $plan->billing_cycle) }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="glass-light rounded-lg p-3 text-center">
                                    <p class="text-xs text-dark-500 mb-1">CPU</p>
                                    <p class="text-sm font-bold text-white">{{ $plan->cpu }}%</p>
                                </div>
                                <div class="glass-light rounded-lg p-3 text-center">
                                    <p class="text-xs text-dark-500 mb-1">RAM</p>
                                    <p class="text-sm font-bold text-white">{{ $plan->memory }}MB</p>
                                </div>
                                <div class="glass-light rounded-lg p-3 text-center">
                                    <p class="text-xs text-dark-500 mb-1">Disk</p>
                                    <p class="text-sm font-bold text-white">{{ $plan->disk }}MB</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 text-xs text-dark-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $plan->swap }}MB Swap
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $plan->databases }} Databases
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $plan->backups }} Backups
                                </span>
                            </div>
                        </div>

                        <div class="sm:w-40 flex flex-col gap-2">
                            <button type="button" @click="selectPlan({{ $plan->id }}, {{ $plan->price }}, '{{ addslashes($plan->name) }}')"
                                    :class="selectedPlan === {{ $plan->id }} ? 'btn-primary text-white shadow-lg shadow-primary-500/25' : 'btn-ghost text-dark-300 hover:text-white'"
                                    class="w-full py-2.5 px-4 text-sm font-medium rounded-xl transition-all text-center">
                                {{ $product->form_fields ? 'Configure' : 'Select' }}
                            </button>
                            @if (empty($product->form_fields))
                                @auth
                                    <form method="POST" action="{{ route('cart.add', $plan) }}">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full py-2.5 px-4 btn-primary text-white text-sm font-medium rounded-xl transition-all hover:shadow-lg hover:shadow-primary-500/25">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('register') }}" class="w-full text-center py-2.5 px-4 bg-dark-700 hover:bg-dark-600 text-dark-300 text-sm font-medium rounded-xl transition block">
                                        Register
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="lg:col-span-1" x-show="selectedPlan" x-cloak x-transition>
            <div class="glass rounded-2xl p-6 sticky top-24">
                <h3 class="text-lg font-display font-bold text-white mb-1">Create Your Server</h3>
                <p class="text-dark-400 text-xs mb-5">Fill in the details to configure your server.</p>

                <form method="POST" action="{{ route('cart.add', '__PLAN__') }}" id="addToCartForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="quantity" :value="quantity">

                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Server Name</label>
                        <input type="text" name="hostname" x-model="hostname" placeholder="my-server" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Game Username</label>
                        <input type="text" name="game_username" x-model="gameUsername" placeholder="Your in-game name" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Email</label>
                        <input type="email" name="email" x-model="email" value="{{ auth()->user()->email ?? '' }}" placeholder="you@example.com" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Quantity</label>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="w-10 h-10 rounded-xl glass-light flex items-center justify-center text-dark-400 hover:text-white transition">-</button>
                            <span class="text-white font-medium min-w-[2rem] text-center" x-text="quantity">1</span>
                            <button type="button" @click="quantity = Math.min(10, quantity + 1)" class="w-10 h-10 rounded-xl glass-light flex items-center justify-center text-dark-400 hover:text-white transition">+</button>
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-4 mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-dark-400" x-text="planName"></span>
                            <span class="text-white" x-text="'$' + planPrice.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm mb-3" x-show="quantity > 1">
                            <span class="text-dark-500" x-text="'x' + quantity + ' servers'"></span>
                            <span class="text-dark-300" x-text="'$' + (planPrice * quantity).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span class="text-white">Total</span>
                            <span class="gradient-text text-lg" x-text="'$' + (planPrice * quantity).toFixed(2)"></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-semibold rounded-xl text-sm hover:shadow-lg hover:shadow-primary-500/25 transition-all">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1" x-show="!selectedPlan" x-cloak>
            <div class="glass rounded-2xl p-6 sticky top-24">
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-white mb-1">Select a Plan</h3>
                    <p class="text-dark-400 text-sm">Choose a plan above to configure your server.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function productPage() {
    return {
        selectedPlan: null,
        planPrice: 0,
        planName: '',
        hostname: '',
        gameUsername: '',
        email: '{{ auth()->user()->email ?? "" }}',
        quantity: 1,
        selectPlan(planId, price, name) {
            this.selectedPlan = planId;
            this.planPrice = price;
            this.planName = name;
            const form = document.getElementById('addToCartForm');
            form.action = '/cart/add/' + planId;
        }
    }
}
</script>
@endsection
