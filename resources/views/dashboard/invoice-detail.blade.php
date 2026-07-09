@extends('layouts.dashboard')
@section('title', 'Invoice ' . $invoice->number)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('dashboard.invoices') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Invoices
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Invoice {{ $invoice->number }}</h1>
                <p class="text-dark-400 mt-1">Created {{ $invoice->created_at->format('M j, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.invoices.pdf', $invoice) }}" class="px-4 py-2 bg-dark-700 text-dark-300 border border-white/10 text-sm font-medium rounded-xl hover:bg-dark-600 transition">
                    Download PDF
                </a>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium
                    {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($invoice->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : ($invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600')) }}">
                    {{ ucfirst($invoice->status) }}
                </span>
                @if ($invoice->status === 'pending')
                    <a href="{{ route('checkout.pay', $invoice) }}" class="px-4 py-2 btn-primary text-white text-sm font-medium rounded-xl">
                        Pay Now
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-display font-bold text-white mb-4">Invoice Items</h2>
                <div class="space-y-3">
                    @foreach ($invoice->items as $item)
                        <div class="glass-light rounded-xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white">{{ $item->description }}</p>
                                <p class="text-xs text-dark-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            <span class="text-sm font-bold text-white">${{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($invoice->transactions->isNotEmpty())
                <div class="glass rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5">
                        <h2 class="text-lg font-display font-bold text-white">Payment History</h2>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach ($invoice->transactions as $transaction)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-white">{{ $transaction->gateway->name ?? 'Credit' }}</p>
                                        <p class="text-xs text-dark-500">{{ $transaction->transaction_id }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-white">${{ number_format($transaction->amount, 2) }}</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                                            {{ $transaction->status === 'succeeded' ? 'bg-green-500/10 text-green-400' : 'bg-dark-700 text-dark-400' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="glass rounded-2xl p-6 sticky top-24">
                <h3 class="text-lg font-display font-bold text-white mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-dark-400">Subtotal</span>
                        <span class="text-white">${{ number_format($totals['subtotal'], 2) }}</span>
                    </div>
                    @if ($totals['tax'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">{{ $totals['tax_name'] }} ({{ $totals['tax_rate'] }}%)</span>
                            <span class="text-white">${{ number_format($totals['tax'], 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-white/5 pt-3 flex justify-between">
                        <span class="font-bold text-white">Total</span>
                        <span class="text-xl font-bold gradient-text">${{ number_format($totals['total'], 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @if ($invoice->due_at)
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">Due Date</span>
                            <span class="text-white">{{ $invoice->due_at->format('M j, Y') }}</span>
                        </div>
                    @endif
                    @if ($invoice->snapshot)
                        <div class="flex justify-between text-sm">
                            <span class="text-dark-400">Bill To</span>
                            <span class="text-white text-right">{{ $invoice->snapshot->bill_to }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
