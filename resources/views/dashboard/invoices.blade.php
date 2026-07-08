@extends('layouts.dashboard')
@section('title', 'Invoices')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">My Invoices</h1>

    @if ($invoices->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-dark-500">No invoices yet.</p>
        </div>
    @else
        <div class="glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Invoice</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Due Date</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4 font-medium text-white text-sm">{{ $invoice->invoice_number }}</td>
                                <td class="px-6 py-4 text-sm text-white">${{ number_format($invoice->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $invoice->due_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                                        {{ $invoice->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                                        {{ $invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                        {{ $invoice->status === 'cancelled' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-primary-400 hover:text-primary-300 text-sm transition">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection