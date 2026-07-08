@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Invoices</h1>

    @if ($invoices->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-xl text-gray-500">No invoices yet.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr class="border-t">
                            <td class="px-6 py-4 font-medium">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4">${{ number_format($invoice->total, 2) }}</td>
                            <td class="px-6 py-4">{{ $invoice->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-sm
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : '' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('dashboard.invoice-detail', $invoice) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
