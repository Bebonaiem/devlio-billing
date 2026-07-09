<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('dashboard.tickets') }}" class="p-2 text-dark-400 hover:text-white rounded-lg hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Ticket #{{ $ticket->id }}</h1>
                <p class="text-dark-400 mt-1">{{ $ticket->subject }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @foreach($ticket->messages as $message)
                    <div class="glass rounded-xl p-5 {{ $message->user_id === auth()->id() ? 'border-l-2 border-primary-500' : '' }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white">
                                {{ substr($message->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $message->user->name }}</p>
                                <p class="text-xs text-dark-400">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-dark-300 whitespace-pre-wrap">{{ $message->message }}</div>
                    </div>
                @endforeach

                @if($ticket->status !== 'closed')
                    <div class="glass rounded-xl p-5">
                        <h3 class="text-sm font-medium text-white mb-3">Reply</h3>
                        <textarea wire:model="message" rows="4" class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm placeholder-dark-500" placeholder="Type your reply..."></textarea>
                        <div class="flex justify-end mt-3">
                            <button wire:click="sendMessage" class="px-4 py-2 btn-primary text-white rounded-lg text-sm font-medium">Send Reply</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="glass rounded-xl p-5">
                    <h3 class="text-sm font-medium text-white mb-3">Ticket Info</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-dark-400">Status</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $ticket->status === 'open' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $ticket->status === 'replied' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                {{ $ticket->status === 'closed' ? 'bg-dark-500/20 text-dark-400' : '' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-dark-400">Priority</span>
                            <span class="text-white">{{ ucfirst($ticket->priority) }}</span>
                        </div>
                        @if($ticket->service)
                            <div class="flex justify-between">
                                <span class="text-dark-400">Service</span>
                                <span class="text-white">{{ $ticket->service->product?->name ?? 'N/A' }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-dark-400">Created</span>
                            <span class="text-white">{{ $ticket->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                @if($ticket->status !== 'closed')
                    <button wire:click="closeTicket" class="w-full px-4 py-2 border border-red-500/20 text-red-400 hover:bg-red-500/10 rounded-lg text-sm font-medium transition-all">Close Ticket</button>
                @endif
            </div>
        </div>
    </div>
</div>
