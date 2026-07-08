@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="mb-8">
        <a href="{{ route('storefront') }}" class="text-blue-600 hover:underline">&larr; Back to Products</a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
        <p class="text-lg text-gray-600 mb-8">{{ $product->description }}</p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($product->plans as $plan)
                <div class="border-2 rounded-lg p-6 hover:border-blue-500 transition text-center">
                    <h3 class="text-xl font-bold mb-2">{{ $plan->name }}</h3>
                    <p class="text-3xl font-bold text-blue-600 mb-4">${{ number_format($plan->price, 2) }}</p>
                    <p class="text-sm text-gray-500 mb-4">per {{ str_replace('_', ' ', $plan->billing_cycle) }}</p>

                    @if ($plan->setup_fee > 0)
                        <p class="text-sm text-gray-500 mb-2">+ ${{ number_format($plan->setup_fee, 2) }} setup fee</p>
                    @endif

                    <ul class="text-left text-sm text-gray-600 space-y-2 mb-6">
                        <li>&check; {{ $plan->cpu }}% CPU</li>
                        <li>&check; {{ $plan->memory }} MB RAM</li>
                        <li>&check; {{ $plan->disk }} MB Disk</li>
                        <li>&check; {{ $plan->swap }} MB Swap</li>
                        <li>&check; {{ $plan->databases }} Databases</li>
                        <li>&check; {{ $plan->backups }} Backups</li>
                    </ul>

                    @auth
                        <a href="{{ route('checkout.index', $plan) }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Order Now
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
                            Register to Order
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
