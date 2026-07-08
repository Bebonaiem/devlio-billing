@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">Product</th>
                <th class="px-6 py-3">Plan</th>
                <th class="px-6 py-3">Amount</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Due Date</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr class="border-t">
                    <td class="px-6 py-4">#{{ $order->id }}</td>
                    <td class="px-6 py-4">{{ $order->user->name }}</td>
                    <td class="px-6 py-4">{{ $order->plan->product->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $order->plan->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">${{ number_format($order->plan->price ?? 0, 2) }}/{{ str_replace('_', ' ', $order->plan->billing_cycle ?? '') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-sm
                            {{ $order->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $order->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : '' }}
                            {{ $order->status === 'terminated' ? 'bg-gray-100 text-gray-500' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $order->next_due_date?->format('M d, Y') ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
