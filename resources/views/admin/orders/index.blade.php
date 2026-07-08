@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Product</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Plan</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Due Date</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                        <td class="px-6 py-4 text-sm text-white">#{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $order->plan->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $order->plan->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-white">${{ number_format($order->plan->price ?? 0, 2) }}/{{ str_replace('_', '-', $order->plan->billing_cycle ?? '') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                {{ $order->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                                {{ $order->status === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                                {{ $order->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}
                                {{ $order->status === 'terminated' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $order->next_due_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-400 hover:text-primary-300 text-sm transition">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection