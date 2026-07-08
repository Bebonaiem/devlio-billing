@extends('layouts.app')
@section('title', $server->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard.servers') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Servers
        </a>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-display font-bold text-white mb-1">{{ $server->name }}</h1>
                <p class="text-dark-400 text-sm">{{ $server->order->plan->product->name ?? 'N/A' }}</p>
            </div>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                {{ $server->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                {{ $server->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                {{ $server->status === 'installing' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}">
                <span class="w-2 h-2 rounded-full {{ $server->status === 'active' ? 'bg-green-400' : ($server->status === 'suspended' ? 'bg-red-400' : 'bg-yellow-400') }}"></span>
                {{ ucfirst($server->status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Resources</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2"><span class="text-dark-400">CPU</span><span class="text-white font-medium">{{ $server->cpu }}%</span></div>
                        <div class="w-full bg-dark-800 rounded-full h-2"><div class="bg-gradient-to-r from-primary-500 to-primary-400 rounded-full h-2" style="width: {{ min($server->cpu, 100) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2"><span class="text-dark-400">Memory</span><span class="text-white font-medium">{{ $server->memory }} MB</span></div>
                        <div class="w-full bg-dark-800 rounded-full h-2"><div class="bg-gradient-to-r from-green-500 to-green-400 rounded-full h-2" style="width: {{ min($server->memory / 100, 100) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2"><span class="text-dark-400">Disk</span><span class="text-white font-medium">{{ $server->disk }} MB</span></div>
                        <div class="w-full bg-dark-800 rounded-full h-2"><div class="bg-gradient-to-r from-purple-500 to-purple-400 rounded-full h-2" style="width: {{ min($server->disk / 1000, 100) }}%"></div></div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-dark-300 uppercase tracking-wider mb-4">Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Plan</dt><dd class="text-white text-sm">{{ $server->order->plan->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Billing</dt><dd class="text-white text-sm capitalize">{{ str_replace('_', ' ', $server->order->plan->billing_cycle ?? '') }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Price</dt><dd class="text-white text-sm">${{ number_format($server->order->plan->price ?? 0, 2) }}</dd></div>
                    <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Node</dt><dd class="text-white text-sm">{{ $server->node ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Created</dt><dd class="text-white text-sm">{{ $server->created_at->format('M d, Y') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection