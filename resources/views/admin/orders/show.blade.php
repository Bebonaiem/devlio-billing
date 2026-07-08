@extends('layouts.admin')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Order Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">ID</dt><dd class="text-white text-sm">#{{ $order->id }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">User</dt><dd class="text-white text-sm">{{ $order->user->name }} ({{ $order->user->email }})</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Amount</dt><dd class="text-white text-sm">${{ number_format($order->amount ?? 0, 2) }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Next Due</dt><dd class="text-white text-sm">{{ $order->next_due_date?->format('M d, Y') ?? 'N/A' }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Created</dt><dd class="text-white text-sm">{{ $order->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <div class="space-y-6">
        @if ($order->server)
            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">Service</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Name</dt><dd class="text-white text-sm">{{ $order->server->name }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Product</dt><dd class="text-white text-sm">{{ $order->server->product->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Plan</dt><dd class="text-white text-sm">{{ $order->server->plan->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Pterodactyl ID</dt><dd class="text-white text-sm">{{ $order->server->pterodactyl_server_id ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Status</dt><dd><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $order->server->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">{{ ucfirst($order->server->status) }}</span></dd></div>
                </dl>
            </div>
        @endif

        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h2 class="text-lg font-display font-bold text-white">Invoices</h2>
            </div>
            @if ($order->invoices->isEmpty())
                <div class="p-8 text-center"><p class="text-dark-500">No invoices.</p></div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">#</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th></tr></thead>
                        <tbody>
                            @foreach ($order->invoices as $invoice)
                                <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                                    <td class="px-6 py-3 text-dark-300">{{ $invoice->number }}</td>
                                    <td class="px-6 py-3 text-white">${{ number_format($invoice->total, 2) }}</td>
                                    <td class="px-6 py-3 text-dark-300">{{ ucfirst($invoice->status) }}</td>
                                    <td class="px-6 py-3 text-dark-400">{{ $invoice->created_at->format('M d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
