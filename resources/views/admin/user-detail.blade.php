@extends('layouts.admin')
@section('title', 'User: ' . $user->name)
@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">User Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">ID</dt><dd class="text-white text-sm">{{ $user->id }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Name</dt><dd class="text-white text-sm">{{ $user->name }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Email</dt><dd class="text-white text-sm">{{ $user->email }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Credit Balance</dt><dd class="gradient-text font-semibold text-sm">${{ number_format($user->credit_balance, 2) }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Affiliate Code</dt><dd class="text-primary-400 font-mono text-sm">{{ $user->affiliate_code }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Pterodactyl ID</dt><dd class="text-white text-sm">{{ $user->pterodactyl_user_id ?? 'N/A' }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Joined</dt><dd class="text-white text-sm">{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Orders ({{ $user->orders->count() }})</h2>
        @forelse ($user->orders as $order)
            <div class="glass rounded-xl p-4 mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-white font-medium">#{{ $order->id }} - {{ $order->plan->product->name ?? 'N/A' }} ({{ $order->plan->name ?? 'N/A' }})</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $order->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($order->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No orders.</p>
        @endforelse
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Invoices ({{ $user->invoices->count() }})</h2>
        @forelse ($user->invoices->take(10) as $invoice)
            <div class="flex justify-between items-center py-3 border-b border-white/5 last:border-0">
                <span class="text-sm text-dark-300">{{ $invoice->invoice_number }}</span>
                <span class="{{ $invoice->status === 'paid' ? 'text-green-400' : ($invoice->status === 'overdue' ? 'text-red-400' : 'text-yellow-400') }} text-sm">
                    ${{ number_format($invoice->total, 2) }} - {{ ucfirst($invoice->status) }}
                </span>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No invoices.</p>
        @endforelse
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Tickets ({{ $user->tickets->count() }})</h2>
        @forelse ($user->tickets as $ticket)
            <div class="flex justify-between items-center py-3 border-b border-white/5 last:border-0">
                <span class="text-sm text-dark-300">{{ $ticket->subject }}</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $ticket->status === 'open' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">{{ ucfirst($ticket->status) }}</span>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No tickets.</p>
        @endforelse
    </div>
</div>
@endsection