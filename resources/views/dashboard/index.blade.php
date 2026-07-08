@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">Welcome back, <span class="gradient-text">{{ $user->name }}</span></h1>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glass rounded-xl p-5 group hover:border-primary-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Active Servers</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['active_servers'] }}</p>
            <a href="{{ route('dashboard.servers') }}" class="text-primary-400 hover:text-primary-300 text-xs mt-2 inline-flex items-center gap-1 transition">Manage <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <div class="glass rounded-xl p-5 group hover:border-yellow-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Pending Invoices</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['pending_invoices'] }}</p>
            <a href="{{ route('dashboard.invoices') }}" class="text-primary-400 hover:text-primary-300 text-xs mt-2 inline-flex items-center gap-1 transition">View <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <div class="glass rounded-xl p-5 group hover:border-green-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Credit Balance</span>
            </div>
            <p class="text-3xl font-bold gradient-text">${{ number_format($user->credit_balance, 2) }}</p>
        </div>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h2 class="text-lg font-display font-bold text-white">Recent Invoices</h2>
        </div>
        @if ($recentInvoices->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-dark-500">No invoices yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Invoice</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Due Date</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentInvoices as $invoice)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-primary-400 hover:text-primary-300 font-medium text-sm transition">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-white">${{ number_format($invoice->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $invoice->due_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($invoice->status === 'overdue' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection