@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">My Profile</h1>

    <div class="glass rounded-2xl p-6 sm:p-8 mb-6">
        <h2 class="text-lg font-display font-bold text-white mb-6">Account Details</h2>
        <dl class="space-y-4">
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Name</dt><dd class="text-white text-sm">{{ $user->name }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Email</dt><dd class="text-white text-sm">{{ $user->email }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Credit Balance</dt><dd class="gradient-text font-semibold text-sm">${{ number_format($user->credit_balance, 2) }}</dd></div>
            <div class="flex justify-between py-2 border-b border-white/5"><dt class="text-dark-400 text-sm">Affiliate Code</dt><dd class="text-primary-400 font-mono text-sm">{{ $user->affiliate_code }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-dark-400 text-sm">Member Since</dt><dd class="text-white text-sm">{{ $user->created_at->format('F d, Y') }}</dd></div>
        </dl>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8">
        <h2 class="text-lg font-display font-bold text-white mb-6">Payment Methods</h2>
        @if ($paymentMethods->isEmpty())
            <div class="text-center py-8">
                <svg class="w-10 h-10 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <p class="text-dark-500 text-sm">No payment methods saved.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($paymentMethods as $pm)
                    <div class="glass rounded-xl p-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-dark-700 flex items-center justify-center">
                                <svg class="w-5 h-5 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <span class="capitalize font-medium text-white text-sm">{{ $pm->gateway }}</span>
                                @if ($pm->last_four)
                                    <span class="text-dark-400 text-sm ml-2">**** {{ $pm->last_four }}</span>
                                @endif
                            </div>
                        </div>
                        @if ($pm->is_default)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">Default</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection