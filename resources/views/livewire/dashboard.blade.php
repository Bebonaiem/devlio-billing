<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Dashboard</h1>
            <p class="text-dark-400 mt-1">Welcome back, {{ $user->first_name }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="glass rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-dark-400 text-sm">Active Services</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['active_services'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    </div>
                </div>
            </div>
            <div class="glass rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-dark-400 text-sm">Pending Invoices</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['pending_invoices'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>
            <div class="glass rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-dark-400 text-sm">Open Tickets</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['open_tickets'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.5-1 2.5-2 3.5-1 1-2 2-2 4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="glass rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-dark-400 text-sm">Total Spent</p>
                        <p class="text-2xl font-bold text-white mt-1">${{ number_format($stats['total_spent'], 2) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Recent Services</h2>
                    <a href="{{ route('dashboard.services') }}" class="text-sm text-primary-400 hover:text-primary-300">View All</a>
                </div>
                @if($recentServices->isEmpty())
                    <p class="text-dark-400 text-sm">No services yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentServices as $service)
                            <a href="{{ route('dashboard.service-detail', $service->id) }}" class="block p-3 rounded-lg hover:bg-white/5 transition-all">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $service->label }}</p>
                                        <p class="text-xs text-dark-400">{{ $service->product?->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $service->status === 'active' ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ $service->status === 'suspended' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                        {{ $service->status === 'pending' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                        {{ $service->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="glass rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Recent Invoices</h2>
                    <a href="{{ route('dashboard.invoices') }}" class="text-sm text-primary-400 hover:text-primary-300">View All</a>
                </div>
                @if($recentInvoices->isEmpty())
                    <p class="text-dark-400 text-sm">No invoices yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentInvoices as $invoice)
                            <a href="{{ route('dashboard.invoice-detail', $invoice->number) }}" class="block p-3 rounded-lg hover:bg-white/5 transition-all">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $invoice->number }}</p>
                                        <p class="text-xs text-dark-400">{{ $invoice->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-white">{{ $invoice->formatted_total }}</p>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : '' }}
                                            {{ $invoice->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                            {{ $invoice->status === 'overdue' ? 'bg-red-500/20 text-red-400' : '' }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="glass rounded-xl p-6 mt-6">
            <h2 class="text-lg font-semibold text-white mb-4">Recent Activity</h2>
            @if($activity->isEmpty())
                <p class="text-dark-400 text-sm">No recent activity.</p>
            @else
                <div class="space-y-3">
                    @foreach($activity as $item)
                        <a href="{{ $item['url'] }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 transition-all">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $item['type'] === 'service' ? 'bg-green-500/20' : '' }}
                                {{ $item['type'] === 'invoice' ? 'bg-yellow-500/20' : '' }}
                                {{ $item['type'] === 'ticket' ? 'bg-blue-500/20' : '' }}">
                                @if($item['type'] === 'service')
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                                @elseif($item['type'] === 'invoice')
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.5-1 2.5-2 3.5-1 1-2 2-2 4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white truncate">{{ $item['title'] }}</p>
                                <p class="text-xs text-dark-400">{{ $item['date']->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $item['status'] === 'active' || $item['status'] === 'paid' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $item['status'] === 'pending' || $item['status'] === 'open' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                {{ $item['status'] === 'suspended' || $item['status'] === 'overdue' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($item['status']) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
