@extends('layouts.admin')
@section('title', 'Services')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">All Services</h2>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Name</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Product</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Plan</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Node</th><th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th></tr></thead>
            <tbody>
                @foreach ($servers as $server)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $server->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $server->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $server->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $server->plan->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $server->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($server->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : ($server->status === 'cancelled' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20')) }}">{{ ucfirst($server->status) }}</span></td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $server->node ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('admin.servers.show', $server) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $servers->links() }}</div>
@endsection
