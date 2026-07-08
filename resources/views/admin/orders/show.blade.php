@extends('layouts.admin')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Order Details</h2>
        <dl class="space-y-2">
            <div class="flex justify-between"><dt class="text-gray-600">ID:</dt><dd>#{{ $order->id }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">User:</dt><dd>{{ $order->user->name }} ({{ $order->user->email }})</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Product:</dt><dd>{{ $order->plan->product->name ?? 'N/A' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Plan:</dt><dd>{{ $order->plan->name ?? 'N/A' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Price:</dt><dd>${{ number_format($order->plan->price ?? 0, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Cycle:</dt><dd class="capitalize">{{ str_replace('_', ' ', $order->plan->billing_cycle ?? '') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Status:</dt><dd><span class="px-2 py-1 rounded text-sm {{ $order->status === 'active' ? 'bg-green-100 text-green-700' : ($order->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($order->status) }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Next Due:</dt><dd>{{ $order->next_due_date?->format('M d, Y') ?? 'N/A' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Created:</dt><dd>{{ $order->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>

        <div class="mt-6 flex gap-2">
            @if ($order->status === 'active')
                <form method="POST" action="{{ route('admin.orders.suspend', $order) }}">
                    @csrf
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Suspend</button>
                </form>
            @endif
            @if ($order->status === 'suspended')
                <form method="POST" action="{{ route('admin.orders.unsuspend', $order) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Unsuspend</button>
                </form>
            @endif
            @if ($order->status !== 'terminated')
                <form method="POST" action="{{ route('admin.orders.terminate', $order) }}" onsubmit="return confirm('Terminate this order?')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Terminate</button>
                </form>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        @if ($order->server)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Server</h2>
                <dl class="space-y-2">
                    <div class="flex justify-between"><dt class="text-gray-600">Name:</dt><dd>{{ $order->server->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Pterodactyl ID:</dt><dd>{{ $order->server->pterodactyl_server_id ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Status:</dt><dd><span class="px-2 py-1 rounded text-sm {{ $order->server->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($order->server->status) }}</span></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Resources:</dt><dd>{{ $order->server->cpu }}% / {{ $order->server->memory }}MB / {{ $order->server->disk }}MB</dd></div>
                </dl>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Invoices</h2>
            @if ($order->invoices->isEmpty())
                <p class="text-gray-500">No invoices.</p>
            @else
                <table class="w-full text-sm">
                    <thead><tr class="border-b text-left"><th class="pb-2">#</th><th class="pb-2">Amount</th><th class="pb-2">Status</th><th class="pb-2">Date</th></tr></thead>
                    <tbody>
                        @foreach ($order->invoices as $invoice)
                            <tr class="border-b">
                                <td class="py-2">{{ $invoice->invoice_number }}</td>
                                <td class="py-2">${{ number_format($invoice->total, 2) }}</td>
                                <td class="py-2">{{ ucfirst($invoice->status) }}</td>
                                <td class="py-2">{{ $invoice->created_at->format('M d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
