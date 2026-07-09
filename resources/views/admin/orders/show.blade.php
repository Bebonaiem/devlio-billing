@extends('layouts.admin')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-display font-bold text-white">Order #{{ $order->id }}</h2>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.orders.suspend', $order) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded-xl text-sm font-medium hover:bg-yellow-500/20">Suspend</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.unsuspend', $order) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-500/10 text-green-400 border border-green-500/20 rounded-xl text-sm font-medium hover:bg-green-500/20">Unsuspend</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.terminate', $order) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-sm font-medium hover:bg-red-500/20">Terminate</button>
        </form>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Order Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">ID</dt><dd class="text-white text-sm">#{{ $order->id }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">User</dt><dd class="text-white text-sm">{{ $order->user->name }} ({{ $order->user->email }})</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Currency</dt><dd class="text-white text-sm">{{ strtoupper($order->currency_code) }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Created</dt><dd class="text-white text-sm">{{ $order->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h2 class="text-lg font-display font-bold text-white">Services</h2>
        </div>
        @if ($order->services->isEmpty())
            <div class="p-8 text-center"><p class="text-dark-500">No services in this order.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">ID</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Product</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Next Due</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th></tr></thead>
                    <tbody>
                        @foreach ($order->services as $service)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                                <td class="px-6 py-3 text-white">#{{ $service->id }}</td>
                                <td class="px-6 py-3 text-dark-300">{{ $service->product->name ?? ($service->plan->priceable->name ?? 'N/A') }}</td>
                                <td class="px-6 py-3 text-white">${{ number_format($service->price ?? 0, 2) }}</td>
                                <td class="px-6 py-3 text-dark-400">{{ $service->expires_at?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-3"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($service->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst($service->status) }}</span></td>
                            </tr>
                            @if ($service->server)
                            <tr class="bg-white/[0.02]">
                                <td colspan="5" class="px-6 py-2 text-xs text-dark-400">
                                    Server: {{ $service->server->name ?? 'N/A' }} ({{ $service->server->status }})
                                    @if ($service->server->pterodactyl_server_id) | Pterodactyl ID: {{ $service->server->pterodactyl_server_id }} @endif
                                </td>
                            </tr>
                            @endif
                            @if ($service->invoices->isNotEmpty())
                            <tr class="bg-white/[0.02]">
                                <td colspan="5" class="px-6 py-2">
                                    <details>
                                        <summary class="text-xs text-primary-400 cursor-pointer">Invoices ({{ $service->invoices->count() }})</summary>
                                        <div class="mt-1">
                                            @foreach ($service->invoices as $inv)
                                                <div class="text-xs text-dark-400 py-0.5">{{ $inv->number }} - ${{ number_format($inv->items->sum(fn($i) => $i->price * $i->quantity), 2) }} - <span class="{{ $inv->status === 'paid' ? 'text-green-400' : ($inv->status === 'overdue' ? 'text-red-400' : 'text-yellow-400') }}">{{ ucfirst($inv->status) }}</span></div>
                                            @endforeach
                                        </div>
                                    </details>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<div class="mt-6">
    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Delete this order and all associated services?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Delete Order</button>
    </form>
</div>
@endsection
