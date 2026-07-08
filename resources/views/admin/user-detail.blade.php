@extends('layouts.admin')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">User Details</h2>
        <dl class="space-y-2">
            <div class="flex justify-between"><dt class="text-gray-600">ID:</dt><dd>{{ $user->id }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Name:</dt><dd>{{ $user->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Email:</dt><dd>{{ $user->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Credit Balance:</dt><dd class="font-semibold text-green-600">${{ number_format($user->credit_balance, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Affiliate Code:</dt><dd class="font-mono">{{ $user->affiliate_code }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Pterodactyl ID:</dt><dd>{{ $user->pterodactyl_user_id ?? 'N/A' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Joined:</dt><dd>{{ $user->created_at->format('M d, Y H:i') }}</dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Orders ({{ $user->orders->count() }})</h2>
        @forelse ($user->orders as $order)
            <div class="border rounded p-3 mb-2">
                <div class="flex justify-between">
                    <span class="font-medium">#{{ $order->id }} - {{ $order->plan->product->name ?? 'N/A' }} ({{ $order->plan->name ?? 'N/A' }})</span>
                    <span class="px-2 py-1 rounded text-sm {{ $order->status === 'active' ? 'bg-green-100 text-green-700' : ($order->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No orders.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Invoices ({{ $user->invoices->count() }})</h2>
        @forelse ($user->invoices->take(10) as $invoice)
            <div class="border-b py-2 flex justify-between">
                <span>{{ $invoice->invoice_number }}</span>
                <span class="{{ $invoice->status === 'paid' ? 'text-green-600' : ($invoice->status === 'overdue' ? 'text-red-600' : 'text-yellow-600') }}">
                    ${{ number_format($invoice->total, 2) }} - {{ ucfirst($invoice->status) }}
                </span>
            </div>
        @empty
            <p class="text-gray-500">No invoices.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Tickets ({{ $user->tickets->count() }})</h2>
        @forelse ($user->tickets as $ticket)
            <div class="border-b py-2">
                <div class="flex justify-between">
                    <span>{{ $ticket->subject }}</span>
                    <span class="px-2 py-1 rounded text-sm {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No tickets.</p>
        @endforelse
    </div>
</div>
@endsection
