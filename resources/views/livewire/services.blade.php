<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">My Services</h1>
                <p class="text-dark-400 mt-1">Manage your active services</p>
            </div>
            <a href="{{ route('storefront') }}" class="px-4 py-2 btn-primary text-white rounded-lg text-sm font-medium">Browse Store</a>
        </div>

        <div class="flex gap-4 mb-6">
            <div class="flex-1">
                <input type="text" wire:model.live="search" placeholder="Search services..." class="w-full px-4 py-2.5 rounded-lg input-field text-white text-sm placeholder-dark-500">
            </div>
            <select wire:model.live="status" class="px-4 py-2.5 rounded-lg input-field text-white text-sm">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="suspended">Suspended</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        @if($services->isEmpty())
            <div class="glass rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                <p class="text-dark-400">No services found.</p>
                <a href="{{ route('storefront') }}" class="mt-4 inline-block px-4 py-2 btn-primary text-white rounded-lg text-sm">Browse Store</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($services as $service)
                    <a href="{{ route('dashboard.service-detail', $service->id) }}" class="block glass rounded-xl p-5 hover:bg-white/5 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-500/20 to-purple-500/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-white">{{ $service->label }}</h3>
                                    <p class="text-sm text-dark-400">{{ $service->product?->name ?? 'N/A' }} - {{ $service->plan?->name ?? 'N/A' }}</p>
                                    @if($service->expires_at)
                                        <p class="text-xs text-dark-500 mt-1">Expires: {{ $service->expires_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $service->status === 'active' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $service->status === 'pending' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $service->status === 'suspended' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $service->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                                <svg class="w-5 h-5 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
