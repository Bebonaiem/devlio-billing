@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="glass rounded-2xl p-8">
        <h1 class="text-2xl font-display font-bold text-white mb-6">Checkout</h1>

        <div class="glass rounded-xl p-5 mb-6">
            <h2 class="font-semibold text-white">{{ $plan->product->name }} - {{ $plan->name }}</h2>
            <div class="mt-2 text-sm text-dark-400">
                <p>{{ $plan->cpu }}% CPU | {{ $plan->memory }}MB RAM | {{ $plan->disk }}MB Disk</p>
            </div>
        </div>

        <div class="glass rounded-xl p-5 mb-6">
            <table class="w-full">
                <tr class="border-b border-white/5">
                    <td class="py-3 text-sm text-dark-300">{{ $plan->name }} ({{ str_replace('_', '-', $plan->billing_cycle) }})</td>
                    <td class="py-3 text-sm text-white text-right">${{ number_format($plan->price, 2) }}</td>
                </tr>
                @if ($plan->setup_fee > 0)
                <tr class="border-b border-white/5">
                    <td class="py-3 text-sm text-dark-300">Setup Fee</td>
                    <td class="py-3 text-sm text-white text-right">${{ number_format($plan->setup_fee, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="py-3 font-bold text-white">Total</td>
                    <td class="py-3 font-bold gradient-text text-right text-lg">${{ number_format($plan->price + $plan->setup_fee, 2) }}</td>
                </tr>
            </table>
        </div>

        <form method="POST" action="{{ route('checkout.process', $plan) }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-3">Payment Method</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 glass rounded-xl cursor-pointer hover:border-primary-500/30 transition-all">
                        <input type="radio" name="gateway" value="stripe" checked class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 focus:ring-primary-500 focus:ring-offset-0">
                        <div class="ml-3">
                            <span class="font-medium text-white text-sm">Credit Card (Stripe)</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 glass rounded-xl cursor-pointer hover:border-primary-500/30 transition-all">
                        <input type="radio" name="gateway" value="paypal" class="w-4 h-4 text-primary-500 bg-dark-800 border-dark-600 focus:ring-primary-500 focus:ring-offset-0">
                        <div class="ml-3">
                            <span class="font-medium text-white text-sm">PayPal</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 btn-primary text-white font-semibold rounded-xl text-sm">
                Pay ${{ number_format($plan->price + $plan->setup_fee, 2) }}
            </button>
        </form>
    </div>
</div>
@endsection