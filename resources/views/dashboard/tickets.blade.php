@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Support Tickets</h1>

    @if ($tickets->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-xl text-gray-500 mb-4">No tickets yet.</p>
            <a href="{{ route('storefront') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Contact Support</a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Related Order</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr class="border-t">
                            <td class="px-6 py-4 font-medium">{{ $ticket->subject }}</td>
                            <td class="px-6 py-4">{{ $ticket->order_id ? '#' . $ticket->order_id : 'N/A' }}</td>
                            <td class="px-6 py-4 capitalize">{{ $ticket->priority }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-sm {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $ticket->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection
