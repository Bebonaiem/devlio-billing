@extends('layouts.admin')
@section('title', 'Ticket: ' . $ticket->subject)
@section('content')
<div class="max-w-4xl">
    <div class="glass rounded-2xl p-6 sm:p-8 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
            <div>
                <h2 class="text-lg font-display font-bold text-white mb-1">{{ $ticket->subject }}</h2>
                <p class="text-sm text-dark-400">by {{ $ticket->user->name }} <span class="text-dark-500">·</span> {{ $ticket->created_at->format('M d, Y H:i') }} @if($ticket->order) <span class="text-dark-500">·</span> Order #{{ $ticket->order_id }} @endif</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium {{ $ticket->status === 'open' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">{{ ucfirst($ticket->status) }}</span>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 capitalize">{{ $ticket->priority }}</span>
            </div>
        </div>

        <div class="space-y-4 mb-6">
            @foreach ($ticket->messages as $msg)
                <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $msg->user_id === auth()->id() ? 'bg-primary-500/20 border-primary-500/30' : 'bg-dark-800/50 border-white/5' }} border rounded-xl px-5 py-3">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-medium {{ $msg->user->isAdmin() ? 'text-primary-400' : 'text-dark-300' }}">{{ $msg->user->isAdmin() ? 'Staff' : $msg->user->name }}</span>
                            <span class="text-[10px] text-dark-500">{{ $msg->created_at->format('M d, H:i') }}</span>
                            @if($msg->user->isAdmin())
                                <span class="px-1.5 py-0.5 rounded text-[10px font-medium bg-primary-500/20 text-primary-400">Staff</span>
                            @endif
                        </div>
                        <p class="text-sm text-dark-200 whitespace-pre-wrap">{{ $msg->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8 mb-6">
        <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Reply</h3>
        <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
            @csrf
            <textarea name="message" rows="4" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" placeholder="Type your reply..." required></textarea>
            <div class="flex items-center gap-2 mt-4">
                <button type="submit" class="px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Send Reply</button>
                @if ($ticket->status !== 'resolved')
                    <button type="button" onclick="confirm('Close this ticket?') && document.getElementById('closeForm').submit()" class="px-5 py-2.5 bg-dark-700 hover:bg-dark-600 text-dark-300 text-sm font-medium rounded-xl transition">Close Ticket</button>
                @else
                    <form method="POST" action="{{ route('admin.tickets.reopen', $ticket) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-yellow-500/20 text-yellow-400 text-sm font-medium rounded-xl hover:bg-yellow-500/30 transition">Reopen</button>
                    </form>
                @endif
            </div>
        </form>
        <form id="closeForm" method="POST" action="{{ route('admin.tickets.close', $ticket) }}" class="hidden">@csrf</form>
    </div>

    <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket and all messages?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Delete Ticket</button>
    </form>
</div>
@endsection