@extends('layouts.app')
@section('title', 'My Servers')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">My Servers</h1>

    @if ($servers->isEmpty())
        <div class="glass rounded-2xl p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
            <h3 class="text-lg font-medium text-dark-300 mb-2">No servers yet</h3>
            <p class="text-dark-500 text-sm mb-6">You don't have any servers. Browse our products to get started.</p>
            <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
                Browse Products
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($servers as $server)
                <a href="{{ route('dashboard.server-detail', $server) }}" class="glass rounded-xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-primary-500/30 transition-all group block">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white group-hover:text-primary-300 transition">{{ $server->name }}</h3>
                            <p class="text-sm text-dark-400">{{ $server->order->plan->product->name ?? 'N/A' }} - {{ $server->order->plan->name ?? 'N/A' }}</p>
                            <p class="text-xs text-dark-500 mt-1">{{ $server->cpu }}% CPU | {{ $server->memory }}MB RAM | {{ $server->disk }}MB Disk</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                            {{ $server->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                            {{ $server->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                            {{ $server->status === 'installing' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                            {{ $server->status === 'terminated' ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $server->status === 'active' ? 'bg-green-400' : ($server->status === 'suspended' ? 'bg-red-400' : 'bg-yellow-400') }}"></span>
                            {{ ucfirst($server->status) }}
                        </span>
                        <svg class="w-4 h-4 text-dark-500 group-hover:text-primary-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $servers->links() }}</div>
    @endif
</div>
@endsection