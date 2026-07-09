@extends('layouts.admin')
@section('title', 'Invoice ' . $invoice->number)
@section('content')
<div class="max-w-4xl">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="glass rounded-2xl p-6 sm:p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-lg font-display font-bold text-white mb-1">Invoice {{ $invoice->number }}</h2>
                    <p class="text-sm text-dark-400">{{ $invoice->user->name }} <span class="text-dark-600">·</span> {{ $invoice->created_at->format('M d, Y') }}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($invoice->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : ($invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-dark-500/10 text-dark-400 border border-dark-500/20')) }}">{{ ucfirst($invoice->status) }}</span>
            </div>

            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">User</dt><dd class="text-white text-sm">{{ $invoice->user->name }} ({{ $invoice->user->email }})</dd></div>
                <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Due Date</dt><dd class="text-white text-sm">{{ $invoice->due_at?->format('M d, Y') ?? 'N/A' }}</dd></div>
                <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Total</dt><dd class="gradient-text font-bold">${{ number_format($totals['total'] ?? 0, 2) }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6">
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5"><h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider">Items</h3></div>
                <div class="p-4">
                    @forelse ($invoice->items as $item)
                        <div class="flex justify-between py-2 border-b border-white/5 last:border-0">
                            <span class="text-sm text-dark-300">{{ $item->description }}</span>
                            <span class="text-sm text-white">${{ number_format($item->price, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-dark-500">No items.</p>
                    @endforelse
                </div>
            </div>

            @if ($invoice->transactions->isNotEmpty())
                <div class="glass rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5"><h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider">Transactions</h3></div>
                    <div class="p-4">
                        @foreach ($invoice->transactions as $txn)
                            <div class="flex justify-between py-2 border-b border-white/5 last:border-0">
                                <span class="text-sm text-dark-300">{{ $txn->gateway->name ?? $txn->gateway_id }} <span class="text-dark-500">{{ $txn->created_at->format('M d, H:i') }}</span></span>
                                <span class="text-sm {{ $txn->status === 'succeeded' ? 'text-green-400' : 'text-red-400' }}">${{ number_format($txn->amount, 2) }} ({{ $txn->status }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="glass rounded-2xl p-6 mt-6">
        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Actions</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="px-4 py-2 bg-dark-700 text-dark-300 border border-white/10 rounded-xl text-sm font-medium hover:bg-dark-600">Download PDF</a>
            @if ($invoice->status !== 'paid')
                <form method="POST" action="{{ route('admin.invoices.paid', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500/20 text-green-400 border border-green-500/30 rounded-xl text-sm font-medium hover:bg-green-500/30">Mark Paid</button>
                </form>
            @endif
            @if ($invoice->status === 'pending')
                <form method="POST" action="{{ route('admin.invoices.overdue', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500/20 text-red-400 border border-red-500/30 rounded-xl text-sm font-medium hover:bg-red-500/30">Mark Overdue</button>
                </form>
            @endif
            @if ($invoice->status !== 'cancelled')
                <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}" class="inline" onsubmit="return confirm('Cancel this invoice?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-dark-700 text-dark-300 border border-white/10 rounded-xl text-sm font-medium hover:bg-dark-600">Cancel Invoice</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete this invoice?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-sm font-medium hover:bg-red-500/20">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection