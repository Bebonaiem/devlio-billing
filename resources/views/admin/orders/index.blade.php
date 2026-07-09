@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">All Orders</h2>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Services</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Created</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                        <td class="px-6 py-4 text-sm text-white">#{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $order->services->count() }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-400 hover:text-primary-300 text-sm transition">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-dark-500">No orders found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
