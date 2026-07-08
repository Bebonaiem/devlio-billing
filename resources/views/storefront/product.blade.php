@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="productPage()">
    <div class="mb-8">
        <a href="{{ $product->category ? route('storefront.category', $product->category->slug) : route('storefront') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $product->category ? $product->category->name : 'Products' }}
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
                @php
                    $price = $plan->prices->first();
                @endphp
                <div class="glass rounded-2xl p-6 transition-all hover:border-primary-500/30"
                     :class="{ 'border border-primary-500/50 shadow-lg shadow-primary-500/10': selectedPlan === {{ $plan->id }} }">
                    <div class="flex flex-col sm:flex-row gap-5">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-xl font-display font-bold text-white">{{ $plan->name }}</h3>
                                    <span class="text-xs text-dark-500 mt-1">{{ ucfirst(str_replace('-', ' ', $plan->type)) }}</span>
                                    @if ($price && $price->setup_fee > 0)
                                        <p class="text-xs text-dark-500 mt-1">+ ${{ number_format($price->setup_fee, 2) }} setup fee</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if ($plan->type === 'free')
                                        <span class="text-2xl font-bold text-green-400">Free</span>
                                    @elseif ($price)
                                        <span class="text-2xl font-bold gradient-text">${{ number_format($price->price, 2) }}</span>
                                        @if ($plan->type === 'recurring')
                                            <span class="text-dark-500 text-sm">/{{ $plan->billing_unit ?? 'mo' }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if ($plan->type === 'recurring' && $plan->billing_period)
                                <div class="mb-4">
                                    <span class="text-xs text-dark-400">Billed every {{ $plan->billing_period }} {{ $plan->billing_unit }}(s)</span>
                                </div>
                            @endif
                        </div>

                        <div class="sm:w-40 flex flex-col gap-2">
                            <button type="button" @click="selectPlan({{ $plan->id }}, {{ $price ? $price->price : 0 }}, '{{ addslashes($plan->name) }}', '{{ $plan->type }}')"
                                    :class="selectedPlan === {{ $plan->id }} ? 'btn-primary text-white shadow-lg shadow-primary-500/25' : 'btn-ghost text-dark-300 hover:text-white'"
                                    class="w-full py-2.5 px-4 text-sm font-medium rounded-xl transition-all text-center">
                                {{ $product->configOptions->isNotEmpty() ? 'Configure' : 'Select' }}
                            </button>
                            @if ($product->configOptions->isEmpty())
                                @auth
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
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
                <h3 class="text-lg font-display font-bold text-white mb-1">Configure Your Server</h3>
                <p class="text-dark-400 text-xs mb-5">Select options and add to cart.</p>

                <form method="POST" action="{{ route('cart.add') }}" id="addToCartForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="plan_id" :value="selectedPlan">
                    <input type="hidden" name="quantity" value="1">

                    @if ($product->configOptions->isNotEmpty())
                        @foreach ($product->configOptions as $configOption)
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-1.5">{{ $configOption->name }}</label>
                                @if ($configOption->type === 'text')
                                    <input type="text" name="config_options[{{ $configOption->id }}]" placeholder="{{ $configOption->description }}" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                                @elseif ($configOption->type === 'select')
                                    <select name="config_options[{{ $configOption->id }}]" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white">
                                        <option value="">Select...</option>
                                        @foreach ($configOption->children as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($configOption->type === 'number')
                                    <input type="number" name="config_options[{{ $configOption->id }}]" placeholder="{{ $configOption->description }}" min="0" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <div class="border-t border-white/5 pt-4 mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-dark-400" x-text="planName"></span>
                            <span class="text-white" x-text="'$' + planPrice.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span class="text-white">Total</span>
                            <span class="gradient-text text-lg" x-text="'$' + planPrice.toFixed(2)"></span>
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
        planType: '',
        selectPlan(planId, price, name, type) {
            this.selectedPlan = planId;
            this.planPrice = price;
            this.planName = name;
            this.planType = type;
        }
    }
}
</script>
@endsection
