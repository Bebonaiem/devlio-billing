<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Support Tickets</h1>
                <p class="text-dark-400 mt-1">Get help with your services</p>
            </div>
            <a href="{{ route('dashboard.tickets.create') }}" class="px-4 py-2 btn-primary text-white rounded-lg text-sm font-medium">Create Ticket</a>
        </div>

        @if($tickets->isEmpty())
            <div class="glass rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.5-1 2.5-2 3.5-1 1-2 2-2 4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-dark-400">No tickets yet.</p>
                <a href="{{ route('dashboard.tickets.create') }}" class="mt-4 inline-block px-4 py-2 btn-primary text-white rounded-lg text-sm">Create Ticket</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($tickets as $ticket)
                    <a href="{{ route('dashboard.ticket-detail', $ticket->id) }}" class="block glass rounded-xl p-5 hover:bg-white/5 transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-white">#{{ $ticket->id }} - {{ $ticket->subject }}</h3>
                                <p class="text-sm text-dark-400 mt-1">{{ $ticket->service?->product?->name ?? 'General' }} | {{ $ticket->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $ticket->status === 'open' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $ticket->status === 'replied' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $ticket->status === 'closed' ? 'bg-dark-500/20 text-dark-400' : '' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                                <svg class="w-5 h-5 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
