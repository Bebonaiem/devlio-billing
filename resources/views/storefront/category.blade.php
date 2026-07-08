@extends('layouts.app')
@section('title', $category->name . ' - Products')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('storefront') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-display font-bold text-white">{{ $category->name }}</h1>
        @if ($category->description)
            <p class="text-dark-400 mt-2">{{ $category->description }}</p>
        @endif
    </div>

    @if ($products->isNotEmpty())
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
                        <h3 class="text-lg font-display font-bold text-white mb-2">{{ $product->name }}</h3>
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
    @else
        <div class="text-center py-20">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-lg font-medium text-dark-300 mb-1">No products in this category</h3>
            <p class="text-dark-500 text-sm">Check back soon for new products.</p>
        </div>
    @endif
</div>
@endsection
