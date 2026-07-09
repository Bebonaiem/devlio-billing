@extends('layouts.app')
@section('title', 'Pay Invoice')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Pay Invoice</h1>
                <p class="text-dark-400 mt-1">{{ $invoice->number }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium
                {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' }}">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>

        @php
            $subtotal = $invoice->items->sum(fn($item) => $item->price * $item->quantity);
        @endphp

        <div class="space-y-3 mb-6">
            @foreach ($invoice->items as $item)
                <div class="flex justify-between py-2 border-b border-white/5">
                    <span class="text-sm text-dark-300">{{ $item->description }} x{{ $item->quantity }}</span>
                    <span class="text-sm text-white">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between py-2">
                <span class="font-bold text-white">Total</span>
                <span class="font-bold gradient-text">${{ number_format($subtotal, 2) }}</span>
            </div>
        </div>

        @if ($paymentGateways->isNotEmpty())
            <form method="POST" action="{{ route('checkout.pay.process') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <label class="block text-sm font-medium text-dark-300 mb-2">Payment Method</label>
                <div class="space-y-2">
                    @foreach ($paymentGateways as $gateway)
                        <label class="flex items-center gap-3 p-3 rounded-xl glass-light cursor-pointer hover:bg-white/5 transition">
                            <input type="radio" name="gateway" value="{{ $gateway->id }}" {{ $loop->first ? 'checked' : '' }} class="w-4 h-4 text-primary-500 border-dark-600 bg-dark-800 focus:ring-primary-500">
                            <span class="text-sm text-white">{{ $gateway->name }}</span>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Pay Now</button>
            </form>
        @else
            <div class="text-center py-6">
                <p class="text-dark-400">No payment gateways available. Please contact support.</p>
            </div>
        @endif
    </div>
</div>
@endsection