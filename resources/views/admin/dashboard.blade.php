@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
        <p class="text-gray-600">Total Users</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $stats['active_orders'] }}</p>
        <p class="text-gray-600">Active Orders</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_invoices'] }}</p>
        <p class="text-gray-600">Pending Invoices</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-3xl font-bold text-purple-600">${{ number_format($stats['monthly_revenue'], 2) }}</p>
        <p class="text-gray-600">Revenue This Month</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
        @if ($recentOrders->isEmpty())
            <p class="text-gray-500">No orders yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b text-left">
                        <th class="pb-2">ID</th>
                        <th class="pb-2">User</th>
                        <th class="pb-2">Product</th>
                        <th class="pb-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr class="border-b">
                            <td class="py-2">#{{ $order->id }}</td>
                            <td class="py-2">{{ $order->user->name }}</td>
                            <td class="py-2">{{ $order->plan->product->name ?? 'N/A' }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-sm {{ $order->status === 'active' ? 'bg-green-100 text-green-700' : ($order->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Transactions</h2>
        @if ($recentTransactions->isEmpty())
            <p class="text-gray-500">No transactions yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b text-left">
                        <th class="pb-2">User</th>
                        <th class="pb-2">Gateway</th>
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentTransactions as $txn)
                        <tr class="border-b">
                            <td class="py-2">{{ $txn->user->name }}</td>
                            <td class="py-2 capitalize">{{ $txn->gateway }}</td>
                            <td class="py-2">${{ number_format($txn->amount, 2) }}</td>
                            <td class="py-2">{{ $txn->created_at->format('M d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
