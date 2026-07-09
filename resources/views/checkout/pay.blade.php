@extends('layouts.dashboard')
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

        <div class="space-y-3 mb-6">
            @foreach ($invoice->items as $item)
                <div class="flex justify-between py-2 border-b border-white/5">
                    <span class="text-sm text-dark-300">{{ $item->description }} x{{ $item->quantity }}</span>
                    <span class="text-sm text-white">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-dark-400">Subtotal</span>
                <span class="text-sm text-white">${{ number_format($totals['subtotal'], 2) }}</span>
            </div>
            @if ($totals['tax'] > 0)
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-dark-400">{{ $totals['tax_name'] ?? 'Tax' }} ({{ $totals['tax_rate'] }}%)</span>
                <span class="text-sm text-white">${{ number_format($totals['tax'], 2) }}</span>
            </div>
            @endif
            @if ($creditsApplied > 0)
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-green-400">Credit Applied</span>
                <span class="text-sm text-green-400">-${{ number_format($creditsApplied, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between py-2">
                <span class="font-bold text-white">Total Due</span>
                <span class="font-bold gradient-text">${{ number_format($remaining, 2) }}</span>
            </div>
        </div>

        @if ($remaining > 0 && $paymentGateways->isNotEmpty())
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
                <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">
                    Pay ${{ number_format($remaining, 2) }}
                </button>
            </form>
        @elseif ($remaining <= 0)
            <div class="text-center py-6">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-green-500/20 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-white font-semibold">Fully Paid!</p>
                <p class="text-dark-400 text-sm mt-1">This invoice has been fully paid by credits.</p>
                <a href="{{ route('dashboard.index') }}" class="mt-4 inline-block px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">Go to Dashboard</a>
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-dark-400">No payment gateways available. Please contact support.</p>
            </div>
        @endif
    </div>
</div>
@endsection