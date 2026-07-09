@extends('layouts.dashboard')
@section('title', 'My Tickets')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Support Tickets</h1>
        <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Ticket
        </a>
    </div>

    @if ($tickets->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p class="text-dark-500 mb-4">No tickets yet.</p>
            <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Contact Support</a>
        </div>
    @else
        <div class="glass rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Subject</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Service</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Priority</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4 font-medium text-white text-sm">{{ $ticket->subject }}</td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $ticket->service_id ? '#' . $ticket->service_id : 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-dark-300 capitalize">{{ $ticket->priority }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $ticket->status === 'open' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : ($ticket->status === 'resolved' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $ticket->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection