@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">Checkout</h1>

        <div class="border rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-lg">{{ $plan->product->name }} - {{ $plan->name }}</h2>
            <div class="mt-2 text-sm text-gray-600">
                <p>{{ $plan->cpu }}% CPU | {{ $plan->memory }}MB RAM | {{ $plan->disk }}MB Disk</p>
            </div>
        </div>

        <div class="border rounded-lg p-4 mb-6">
            <table class="w-full">
                <tr class="border-b">
                    <td class="py-2">{{ $plan->name }} ({{ str_replace('_', ' ', $plan->billing_cycle) }})</td>
                    <td class="py-2 text-right">${{ number_format($plan->price, 2) }}</td>
                </tr>
                @if ($plan->setup_fee > 0)
                <tr class="border-b">
                    <td class="py-2">Setup Fee</td>
                    <td class="py-2 text-right">${{ number_format($plan->setup_fee, 2) }}</td>
                </tr>
                @endif
                <tr class="font-bold text-lg">
                    <td class="py-2">Total</td>
                    <td class="py-2 text-right">${{ number_format($plan->price + $plan->setup_fee, 2) }}</td>
                </tr>
            </table>
        </div>

        <form method="POST" action="{{ route('checkout.process', $plan) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2">Payment Method</label>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:border-blue-500">
                        <input type="radio" name="gateway" value="stripe" checked class="mr-3">
                        <span class="font-medium">Credit Card (Stripe)</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:border-blue-500">
                        <input type="radio" name="gateway" value="paypal" class="mr-3">
                        <span class="font-medium">PayPal</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-lg">
                Pay ${{ number_format($plan->price + $plan->setup_fee, 2) }}
            </button>
        </form>
    </div>
</div>
@endsection
