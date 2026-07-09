@extends('layouts.dashboard')
@section('title', 'Server Status')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-display font-bold text-white">Server Status</h1>
            <p class="text-dark-400 mt-1">Monitor your servers and their resource usage.</p>
        </div>
        <a href="{{ route('dashboard.services') }}" class="inline-flex items-center gap-2 px-4 py-2 btn-ghost text-dark-300 text-sm font-medium rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Services
        </a>
    </div>

    @if ($servers->isEmpty())
        <div class="glass rounded-2xl p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-dark-800 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-2">No servers yet</h3>
            <p class="text-dark-400 mb-6">You don't have any servers. Browse our products to get started.</p>
            <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($servers as $item)
                @php
                    $service = $item['service'];
                    $server = $item['server'];
                    $status = $item['status'];
                    $resources = $item['resources'];
                    $ip = $item['ip'];
                    $port = $item['port'];
                @endphp
                <a href="{{ route('dashboard.service-detail', $service) }}" class="glass rounded-2xl p-6 card-hover group">
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-primary-500/20 flex items-center justify-center group-hover:bg-primary-500/30 transition">
                                <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-white group-hover:text-primary-300 transition">{{ $server->name ?? $service->label ?? 'Server' }}</h3>
                                <p class="text-xs text-dark-500">{{ $service->product->name ?? 'N/A' }} &mdash; {{ $service->plan->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium
                            {{ $status === 'running' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                            {{ $status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : '' }}
                            {{ $status === 'stopped' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                            {{ $status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                            {{ $status === 'starting' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                            {{ $status === 'stopping' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : '' }}
                            {{ $status === 'installing' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                            {{ !in_array($status, ['running', 'active', 'stopped', 'suspended', 'starting', 'stopping', 'installing']) ? 'bg-dark-500/10 text-dark-400 border border-dark-500/20' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                {{ $status === 'running' ? 'bg-green-400' : '' }}
                                {{ $status === 'active' ? 'bg-green-400' : '' }}
                                {{ $status === 'stopped' ? 'bg-red-400' : '' }}
                                {{ $status === 'suspended' ? 'bg-red-400' : '' }}
                                {{ $status === 'starting' ? 'bg-yellow-400 animate-pulse' : '' }}
                                {{ $status === 'stopping' ? 'bg-yellow-400 animate-pulse' : '' }}
                                {{ $status === 'installing' ? 'bg-blue-400 animate-pulse' : '' }}
                                {{ !in_array($status, ['running', 'active', 'stopped', 'suspended', 'starting', 'stopping', 'installing']) ? 'bg-dark-400' : '' }}"></span>
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    @if ($ip)
                        <div class="glass-light rounded-lg px-3 py-2 mb-4 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-dark-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            <span class="text-xs text-dark-300 font-mono">{{ $ip }}{{ $port ? ':' . $port : '' }}</span>
                        </div>
                    @endif

                    @if ($resources)
                        <div class="space-y-3">
                            @php
                                $cpuPercent = $resources['cpu_absolute'] ?? null;
                                $memoryUsed = $resources['memory_bytes'] ?? null;
                                $memoryLimit = $resources['memory_limit_bytes'] ?? null;
                                $diskUsed = $resources['disk_bytes'] ?? null;
                                $diskLimit = $resources['disk_limit_bytes'] ?? null;
                                $memoryPercent = ($memoryUsed && $memoryLimit) ? round(($memoryUsed / $memoryLimit) * 100, 1) : null;
                                $diskPercent = ($diskUsed && $diskLimit) ? round(($diskUsed / $diskLimit) * 100, 1) : null;
                            @endphp

                            @if ($cpuPercent !== null)
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs text-dark-400 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                            CPU
                                        </span>
                                        <span class="text-xs font-medium {{ $cpuPercent > 80 ? 'text-red-400' : ($cpuPercent > 60 ? 'text-yellow-400' : 'text-dark-300') }}">{{ round($cpuPercent, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $cpuPercent > 80 ? 'bg-red-500' : ($cpuPercent > 60 ? 'bg-yellow-500' : 'bg-primary-500') }}" style="width: {{ min($cpuPercent, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($memoryPercent !== null)
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs text-dark-400 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            RAM
                                        </span>
                                        <span class="text-xs font-medium {{ $memoryPercent > 80 ? 'text-red-400' : ($memoryPercent > 60 ? 'text-yellow-400' : 'text-dark-300') }}">{{ number_format($memoryUsed / 1024 / 1024, 0) }}MB / {{ number_format($memoryLimit / 1024 / 1024, 0) }}MB</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $memoryPercent > 80 ? 'bg-red-500' : ($memoryPercent > 60 ? 'bg-yellow-500' : 'bg-primary-500') }}" style="width: {{ min($memoryPercent, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($diskPercent !== null)
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs text-dark-400 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                            Disk
                                        </span>
                                        <span class="text-xs font-medium {{ $diskPercent > 80 ? 'text-red-400' : ($diskPercent > 60 ? 'text-yellow-400' : 'text-dark-300') }}">{{ number_format($diskUsed / 1024 / 1024 / 1024, 1) }}GB / {{ number_format($diskLimit / 1024 / 1024 / 1024, 0) }}GB</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $diskPercent > 80 ? 'bg-red-500' : ($diskPercent > 60 ? 'bg-yellow-500' : 'bg-primary-500') }}" style="width: {{ min($diskPercent, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($resources['uptime'])
                                <div class="flex items-center gap-2 pt-1">
                                    <svg class="w-3 h-3 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs text-dark-500">Uptime: {{ \Carbon\CarbonInterval::seconds($resources['uptime'])->forHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif (!$server || !$server->pterodactyl_server_identifier)
                        <div class="glass-light rounded-lg p-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs text-dark-500">Server not provisioned yet</span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
