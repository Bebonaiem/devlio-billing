@extends('layouts.admin')
@section('title', 'User: ' . $user->name)
@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-lg font-display font-bold text-white">User Details</h2>
            <button type="button" onclick="document.getElementById('editForm').classList.toggle('hidden')" class="text-sm text-primary-400 hover:text-primary-300">Edit</button>
        </div>

        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">ID</dt><dd class="text-white text-sm">{{ $user->id }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Name</dt><dd class="text-white text-sm">{{ $user->name }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Email</dt><dd class="text-white text-sm">{{ $user->email }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Credit Balance</dt><dd class="gradient-text font-semibold text-sm">${{ number_format($user->credit_balance, 2) }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Role</dt><dd class="text-white text-sm capitalize">{{ $user->roles->pluck('name')->first() ?? 'customer' }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Affiliate Code</dt><dd class="text-primary-400 font-mono text-sm">{{ $user->affiliate_code ?? 'N/A' }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Pterodactyl ID</dt><dd class="text-white text-sm">{{ $user->pterodactyl_user_id ?? 'N/A' }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Joined</dt><dd class="text-white text-sm">{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <form id="editForm" method="POST" action="{{ route('admin.users.update', $user) }}" class="hidden glass rounded-2xl p-6 sm:p-8 space-y-4">
        @csrf
        <h2 class="text-lg font-display font-bold text-white mb-4">Edit User</h2>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Credit Balance ($)</label>
            <input type="number" name="credit_balance" step="0.01" value="{{ $user->credit_balance }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Role</label>
            <select name="role" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Save Changes</button>
            <button type="button" onclick="document.getElementById('editForm').classList.add('hidden')" class="px-5 py-2.5 bg-dark-700 text-dark-300 text-sm font-medium rounded-xl hover:bg-dark-600">Cancel</button>
        </div>
    </form>

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
                <span class="text-sm text-dark-300">{{ $invoice->number }}</span>
                <span class="{{ $invoice->status === 'paid' ? 'text-green-400' : ($invoice->status === 'overdue' ? 'text-red-400' : 'text-yellow-400') }} text-sm">${{ number_format($invoice->total, 2) }} - {{ ucfirst($invoice->status) }}</span>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No invoices.</p>
        @endforelse
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Tickets ({{ $user->tickets->count() }})</h2>
        @forelse ($user->tickets as $ticket)
            <div class="flex justify-between items-center py-3 border-b border-white/5 last:border-0">
                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-sm text-dark-300 hover:text-primary-300 transition">{{ $ticket->subject }}</a>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $ticket->status === 'open' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">{{ ucfirst($ticket->status) }}</span>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No tickets.</p>
        @endforelse
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Transactions ({{ $user->transactions->count() }})</h2>
        @forelse ($user->transactions->take(10) as $txn)
            <div class="flex justify-between items-center py-3 border-b border-white/5 last:border-0">
                <span class="text-sm text-dark-300">{{ $txn->gateway }} <span class="text-dark-500">{{ $txn->created_at->format('M d') }}</span></span>
                <span class="{{ $txn->status === 'completed' ? 'text-green-400' : 'text-red-400' }} text-sm">${{ number_format($txn->amount, 2) }} ({{ $txn->status }})</span>
            </div>
        @empty
            <p class="text-dark-500 text-sm">No transactions.</p>
        @endforelse
    </div>
</div>
<div class="mt-6">
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user and all related data?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Delete User</button>
    </form>
</div>
@endsection