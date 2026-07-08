@extends('layouts.admin')
@section('title', 'Transactions')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">All Transactions</h2>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">ID</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Gateway</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Invoice</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th><th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th></tr></thead>
            <tbody>
                @foreach ($transactions as $txn)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-sm text-dark-400">#{{ $txn->id }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $txn->user->name }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-dark-300">{{ $txn->gateway }}</td>
                        <td class="px-6 py-4 text-sm text-white">${{ number_format($txn->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $txn->invoice ? $txn->invoice->invoice_number : 'N/A' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $txn->status === 'completed' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($txn->status === 'refunded' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20') }}">{{ ucfirst($txn->status) }}</span></td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $txn->created_at->format('M d, H:i') }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('admin.transactions.show', $txn) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $transactions->links() }}</div>
@endsection