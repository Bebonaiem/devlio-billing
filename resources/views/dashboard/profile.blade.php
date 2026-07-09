@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">Profile Settings</h1>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">Account Information</h2>
                <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                        @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                        @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="px-6 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Save Changes</button>
                </form>
            </div>

            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">Change Password</h2>
                <form method="POST" action="{{ route('dashboard.profile.password') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Current Password</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                        @error('current_password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">New Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                        @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500">
                    </div>
                    <button type="submit" class="px-6 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Update Password</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h3 class="font-display font-bold text-white mb-4">Account Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <dt class="text-dark-400 text-sm">Credit Balance</dt>
                        <dd class="gradient-text font-semibold text-sm">${{ number_format($user->credits->first()?->amount ?? 0, 2) }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <dt class="text-dark-400 text-sm">Two-Factor Auth</dt>
                        <dd>
                            @if ($user->tfa_secret)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Disabled
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <dt class="text-dark-400 text-sm">Affiliate Code</dt>
                        <dd class="text-primary-400 font-mono text-sm">{{ $user->affiliate_code }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-dark-400 text-sm">Member Since</dt>
                        <dd class="text-white text-sm">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="font-display font-bold text-white mb-4">Security</h3>
                <a href="{{ route('dashboard.2fa.show') }}" class="flex items-center gap-3 glass-light rounded-xl p-3 hover:bg-white/10 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-500/30 transition-all">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">Two-Factor Authentication</p>
                        <p class="text-dark-400 text-xs">{{ $user->tfa_secret ? 'Enabled' : 'Not enabled' }}</p>
                    </div>
                    <svg class="w-4 h-4 text-dark-500 ml-auto group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="font-display font-bold text-white mb-4">Payment Methods</h3>
                @if ($paymentMethods->isEmpty())
                    <div class="text-center py-6">
                        <svg class="w-10 h-10 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <p class="text-dark-500 text-sm">No payment methods saved.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($paymentMethods as $pm)
                            <div class="glass-light rounded-xl p-3 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <span class="capitalize text-white text-sm">{{ $pm->gateway }} @if ($pm->last_four) **** {{ $pm->last_four }} @endif</span>
                                </div>
                                @if ($pm->is_default)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">Default</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
