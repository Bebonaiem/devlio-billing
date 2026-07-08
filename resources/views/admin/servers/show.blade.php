@extends('layouts.admin')
@section('title', 'Service: ' . $server->name)
@section('content')
<div class="max-w-3xl">
    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-lg font-display font-bold text-white mb-1">{{ $server->name }}</h2>
                <p class="text-sm text-dark-400">{{ $server->user->name }} <span class="text-dark-600">·</span> {{ $server->created_at->format('M d, Y') }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium {{ $server->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($server->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst($server->status) }}</span>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Config Options</h3>
                @if ($server->configs->isNotEmpty())
                    <dl class="space-y-3">
                        @foreach ($server->configs as $config)
                            <div class="flex justify-between py-2 border-b border-white/5">
                                <dt class="text-dark-400 text-sm">{{ $config->option->name ?? 'N/A' }}</dt>
                                <dd class="text-white text-sm">{{ $config->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="text-sm text-dark-500">No config options set.</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Product</dt><dd class="text-white text-sm">{{ $server->product->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Plan</dt><dd class="text-white text-sm">{{ $server->plan->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Pterodactyl ID</dt><dd class="text-white text-sm">{{ $server->pterodactyl_server_id ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Node</dt><dd class="text-white text-sm">{{ $server->node ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">IP</dt><dd class="text-white text-sm">{{ $server->ip ?? 'N/A' }}</dd></div>
                </dl>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.servers.destroy', $server) }}" class="mt-6 pt-4 border-t border-white/5" onsubmit="return confirm('Delete this service?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Delete Service</button>
        </form>
    </div>
</div>
@endsection
