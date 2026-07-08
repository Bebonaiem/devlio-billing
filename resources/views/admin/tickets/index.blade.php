@extends('layouts.admin')
@section('title', 'Tickets')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">All Tickets</h2>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Subject</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Order</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Priority</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Created</th><th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th></tr></thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $ticket->subject }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $ticket->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $ticket->order_id ? '#'.$ticket->order_id : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-dark-300">{{ $ticket->priority }}</td>
                        <td class="px-6 py-4"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $ticket->status === 'open' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : ($ticket->status === 'resolved' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $ticket->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('admin.tickets.show', $ticket) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $tickets->links() }}</div>
@endsection