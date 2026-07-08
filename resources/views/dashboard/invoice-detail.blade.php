@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold">Invoice</h1>
                <p class="text-gray-500">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 rounded text-lg font-semibold
                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <div>
                <p class="text-sm text-gray-500">Bill To:</p>
                <p class="font-medium">{{ $invoice->user->name }}</p>
                <p class="text-gray-600">{{ $invoice->user->email }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Due Date:</p>
                <p class="font-medium">{{ $invoice->due_date->format('F d, Y') }}</p>
            </div>
        </div>

        <table class="w-full mb-8">
            <thead>
                <tr class="border-b-2">
                    <th class="text-left pb-3">Description</th>
                    <th class="text-right pb-3">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr class="border-b">
                        <td class="py-3">{{ $item->description }}</td>
                        <td class="py-3 text-right">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="py-3 font-bold text-lg">Total</td>
                    <td class="py-3 text-right font-bold text-lg">${{ number_format($invoice->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($invoice->transactions->isNotEmpty())
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-2">Transactions</h3>
                @foreach ($invoice->transactions as $txn)
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>{{ $txn->gateway }} - {{ $txn->created_at->format('M d, Y H:i') }}</span>
                        <span class="{{ $txn->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($txn->amount, 2) }} ({{ $txn->status }})
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($invoice->status === 'pending' || $invoice->status === 'overdue')
            <div class="mt-6">
                <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Pay Now</a>
            </div>
        @endif
    </div>
</div>
@endsection
