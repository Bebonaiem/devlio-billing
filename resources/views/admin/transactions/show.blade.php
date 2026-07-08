@extends('layouts.admin')
@section('title', 'Transaction #' . $transaction->id)
@section('content')
<div class="max-w-3xl">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-lg font-display font-bold text-white mb-1">Transaction #{{ $transaction->id }}</h2>
                <p class="text-sm text-dark-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium {{ $transaction->status === 'completed' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($transaction->status === 'refunded' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20') }}">{{ ucfirst($transaction->status) }}</span>
        </div>

        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">User</dt><dd class="text-white text-sm">{{ $transaction->user->name }} ({{ $transaction->user->email }})</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Gateway</dt><dd class="text-white text-sm capitalize">{{ $transaction->gateway }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Amount</dt><dd class="text-white text-sm">${{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency ?? 'USD') }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Invoice</dt><dd class="text-white text-sm">{{ $transaction->invoice ? $transaction->invoice->number : 'N/A' }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Gateway TXN ID</dt><dd class="text-white text-sm font-mono text-xs">{{ $transaction->gateway_transaction_id ?? 'N/A' }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Gateway Response</dt><dd class="text-white text-sm font-mono text-xs max-w-xs truncate">{{ json_encode($transaction->gateway_response) }}</dd></div>
        </dl>

        @if ($transaction->status === 'completed')
            <div class="mt-6 pt-4 border-t border-white/5">
                <form method="POST" action="{{ route('admin.transactions.refund', $transaction) }}" onsubmit="return confirm('Refund this transaction?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-500/20 text-purple-400 border border-purple-500/30 rounded-xl text-sm font-medium hover:bg-purple-500/30">Refund Transaction</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection