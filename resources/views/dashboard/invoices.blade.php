@extends('layouts.dashboard')
@section('title', 'Invoices')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Invoices</h1>
        <p class="text-dark-400 mt-1">View and manage your invoices.</p>
    </div>

    @if ($invoices->isEmpty())
        <div class="glass rounded-2xl p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-2">No invoices yet</h3>
            <p class="text-dark-400">Your invoices will appear here after placing an order.</p>
        </div>
    @else
        <div class="glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase tracking-wider">Invoice</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th>
                            <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-4 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                            <th class="text-right px-6 py-4 text-xs font-medium text-dark-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($invoices as $invoice)
                            @php
                                $subtotal = $invoice->items->sum(fn($item) => $item->price * $item->quantity);
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-white">{{ $invoice->number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-dark-400">{{ $invoice->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($invoice->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : ($invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600')) }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-white">${{ number_format($subtotal, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a>
                                    @if ($invoice->status === 'pending')
                                        <a href="{{ route('checkout.pay', $invoice) }}" class="text-green-400 hover:text-green-300 text-sm ml-3">Pay</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
