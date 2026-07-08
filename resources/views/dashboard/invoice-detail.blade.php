@extends('layouts.dashboard')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-8 border-b border-white/5">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-2xl font-display font-bold text-white mb-1">Invoice</h1>
                    <p class="text-dark-400 text-sm">{{ $invoice->invoice_number }}</p>
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                    {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                    {{ $invoice->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                    {{ $invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>

        <div class="p-8">
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <p class="text-xs text-dark-500 uppercase tracking-wider mb-2">Bill To</p>
                    <p class="font-medium text-white">{{ $invoice->user->name }}</p>
                    <p class="text-dark-400 text-sm">{{ $invoice->user->email }}</p>
                </div>
                <div class="md:text-right">
                    <p class="text-xs text-dark-500 uppercase tracking-wider mb-2">Due Date</p>
                    <p class="font-medium text-white">{{ $invoice->due_date->format('F d, Y') }}</p>
                </div>
            </div>

            <table class="w-full mb-8">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left pb-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Description</th>
                        <th class="text-right pb-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr class="border-b border-white/5">
                            <td class="py-3 text-sm text-dark-300">{{ $item->description }}</td>
                            <td class="py-3 text-sm text-white text-right">${{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="py-3 font-bold text-lg text-white">Total</td>
                        <td class="py-3 font-bold text-lg gradient-text text-right">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            @if ($invoice->transactions->isNotEmpty())
                <div class="border-t border-white/5 pt-6">
                    <h3 class="font-semibold text-white mb-3">Transactions</h3>
                    <div class="space-y-2">
                        @foreach ($invoice->transactions as $txn)
                            <div class="flex justify-between text-sm py-2">
                                <span class="text-dark-400">{{ $txn->gateway }} - {{ $txn->created_at->format('M d, Y H:i') }}</span>
                                <span class="{{ $txn->status === 'completed' ? 'text-green-400' : 'text-red-400' }}">
                                    ${{ number_format($txn->amount, 2) }} ({{ $txn->status }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($invoice->status === 'pending' || $invoice->status === 'overdue')
                <div class="mt-6">
                    <a href="#" class="inline-flex items-center gap-2 px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">
                        Pay ${{ number_format($invoice->total, 2) }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection