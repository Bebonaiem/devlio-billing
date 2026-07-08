@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Welcome, {{ $user->name }}</h1>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['active_servers'] }}</p>
            <p class="text-gray-600">Active Servers</p>
            <a href="{{ route('dashboard.servers') }}" class="text-blue-600 hover:underline text-sm">Manage &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_invoices'] }}</p>
            <p class="text-gray-600">Pending Invoices</p>
            <a href="{{ route('dashboard.invoices') }}" class="text-blue-600 hover:underline text-sm">View &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">${{ number_format($user->credit_balance, 2) }}</p>
            <p class="text-gray-600">Credit Balance</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Invoices</h2>
        @if ($recentInvoices->isEmpty())
            <p class="text-gray-500">No invoices yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b text-left">
                        <th class="pb-2">Invoice</th>
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Due Date</th>
                        <th class="pb-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentInvoices as $invoice)
                        <tr class="border-b">
                            <td class="py-2">
                                <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-blue-600 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="py-2">${{ number_format($invoice->total, 2) }}</td>
                            <td class="py-2">{{ $invoice->due_date->format('M d, Y') }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-sm {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
