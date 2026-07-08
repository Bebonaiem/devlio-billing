@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-display font-bold text-white">Welcome back, <span class="gradient-text">{{ $user->first_name }}</span></h1>
            <p class="text-dark-400 mt-1">Here's what's happening with your services.</p>
        </div>
        <a href="{{ route('storefront') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 btn-primary text-white text-sm font-medium rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Order
        </a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glass rounded-xl p-5 group hover:border-primary-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Active Services</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['active_services'] }}</p>
            <a href="{{ route('dashboard.services') }}" class="text-primary-400 hover:text-primary-300 text-xs mt-2 inline-flex items-center gap-1 transition">Manage <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <div class="glass rounded-xl p-5 group hover:border-yellow-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Pending Invoices</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['pending_invoices'] }}</p>
            <a href="{{ route('dashboard.invoices') }}" class="text-primary-400 hover:text-primary-300 text-xs mt-2 inline-flex items-center gap-1 transition">View <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <div class="glass rounded-xl p-5 group hover:border-green-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Open Tickets</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['open_tickets'] }}</p>
            <a href="{{ route('dashboard.tickets') }}" class="text-primary-400 hover:text-primary-300 text-xs mt-2 inline-flex items-center gap-1 transition">View <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <div class="glass rounded-xl p-5 group hover:border-purple-500/30 transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-dark-400 text-sm">Total Spent</span>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['total_spent'], 2) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                    <h2 class="text-lg font-display font-bold text-white">Recent Activity</h2>
                </div>
                @if ($activity->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-dark-500">No recent activity.</p>
                        <a href="{{ route('storefront') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Browse Products</a>
                    </div>
                @else
                    <div class="divide-y divide-white/5">
                        @foreach ($activity as $item)
                            <div class="px-6 py-4 hover:bg-white/[0.02] transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center
                                        {{ $item['type'] === 'service' ? 'bg-primary-500/20' : ($item['type'] === 'invoice' ? 'bg-yellow-500/20' : 'bg-green-500/20') }}">
                                        @if ($item['type'] === 'service')
                                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                                        @elseif ($item['type'] === 'invoice')
                                            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white truncate">{{ $item['title'] }}</p>
                                        <p class="text-xs text-dark-500 mt-0.5">{{ $item['date']->diffForHumans() }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium flex-shrink-0
                                        {{ $item['status'] === 'active' || $item['status'] === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($item['status'] === 'pending' || $item['status'] === 'open' || $item['status'] === 'awaiting_reply' ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : 'bg-dark-700 text-dark-400 border border-dark-600') }}">
                                        {{ ucfirst($item['status']) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h2 class="text-lg font-display font-bold text-white">Quick Actions</h2>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('storefront') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-primary-500/20 flex items-center justify-center group-hover:bg-primary-500/30 transition">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">New Order</p>
                            <p class="text-xs text-dark-500">Browse products</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.services') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">My Services</p>
                            <p class="text-xs text-dark-500">Manage your services</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.tickets') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-yellow-500/20 flex items-center justify-center group-hover:bg-yellow-500/30 transition">
                            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Open Ticket</p>
                            <p class="text-xs text-dark-500">Get support</p>
                        </div>
                    </a>
                    <a href="{{ route('dashboard.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Profile</p>
                            <p class="text-xs text-dark-500">Manage your account</p>
                        </div>
                    </a>
                </div>
            </div>

            @if ($stats['open_tickets'] > 0)
                <div class="glass rounded-2xl p-5 border border-yellow-500/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ $stats['open_tickets'] }} open ticket(s)</p>
                            <a href="{{ route('dashboard.tickets') }}" class="text-xs text-yellow-400 hover:text-yellow-300 transition">View tickets</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
