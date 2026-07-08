@extends('layouts.app')

@section('title', 'Game Server Hosting')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Game Server Hosting</h1>
        <p class="text-xl text-gray-600">High-performance game servers with instant setup</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="p-6">
                    @if ($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4 rounded">
                    @endif
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>

                    @if ($product->plans->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($product->plans as $plan)
                                <div class="border rounded p-4 hover:border-blue-500 transition">
                                    <div class="flex justify-between items-center mb-2">
                                        <h3 class="font-semibold">{{ $plan->name }}</h3>
                                        <span class="text-lg font-bold text-blue-600">${{ number_format($plan->price, 2) }}/{{ str_replace('_', '-', $plan->billing_cycle) }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500 space-y-1">
                                        <p>{{ $plan->cpu }}% CPU | {{ $plan->memory }}MB RAM | {{ $plan->disk }}MB Disk</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('checkout.index', $plan) }}" class="mt-3 inline-block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                                            Order Now
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}" class="mt-3 inline-block w-full text-center bg-gray-600 text-white py-2 rounded hover:bg-gray-700 transition">
                                            Register to Order
                                        </a>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p class="text-xl">No products available yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
