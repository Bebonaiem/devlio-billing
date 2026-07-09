@extends('layouts.dashboard')
@section('title', 'Two-Factor Authentication')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">Two-Factor Authentication</h1>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="glass rounded-2xl p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-{{ $isEnabled ? 'green' : 'yellow' }}-500/20 flex items-center justify-center">
                        @if ($isEnabled)
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016a11.955 11.955 0 01-2.667 1.048M12 6.804a5.974 5.974 0 01-2.128 1.036M4.4 5.398c.004.128.006.256.006.384A6.301 6.301 0 006 11.5a6.193 6.193 0 01-1.893.434"/></svg>
                        @else
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-lg font-display font-bold text-white">Status</h2>
                        <p class="text-sm {{ $isEnabled ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ $isEnabled ? 'Enabled' : 'Not Enabled' }}
                        </p>
                    </div>
                </div>

                <p class="text-dark-400 text-sm mb-6">
                    Two-factor authentication adds an extra layer of security to your account. 
                    When enabled, you'll need to enter a code from your authenticator app when signing in.
                </p>

                @if (!$isEnabled)
                    <div class="glass-light rounded-xl p-4 mb-6">
                        <h3 class="text-sm font-medium text-white mb-2">Setup Steps</h3>
                        <ol class="text-xs text-dark-400 space-y-2 list-decimal list-inside">
                            <li>Scan the QR code with your authenticator app</li>
                            <li>Or enter the secret key manually</li>
                            <li>Enter the 6-digit code from your app</li>
                            <li>Click "Enable Two-Factor" to activate</li>
                        </ol>
                    </div>
                @endif
            </div>

            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">
                    {{ $isEnabled ? 'Disable Two-Factor' : 'Enable Two-Factor' }}
                </h2>

                @if ($isEnabled)
                    <p class="text-dark-400 text-sm mb-4">
                        Enter the 6-digit code from your authenticator app to disable two-factor authentication.
                    </p>

                    <form method="POST" action="{{ route('dashboard.2fa.disable') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1.5">Verification Code</label>
                            <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" 
                                   class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500 text-center tracking-[0.5em] font-mono"
                                   placeholder="000000" required autofocus>
                            @error('code') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-medium rounded-xl border border-red-500/20 transition-all">
                            Disable Two-Factor
                        </button>
                    </form>
                @else
                    <p class="text-dark-400 text-sm mb-4">
                        Enter the 6-digit code from your authenticator app to enable two-factor authentication.
                    </p>

                    <form method="POST" action="{{ route('dashboard.2fa.enable') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="secret" value="{{ $secret }}">
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1.5">Verification Code</label>
                            <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" 
                                   class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500 text-center tracking-[0.5em] font-mono"
                                   placeholder="000000" required autofocus>
                            @error('code') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="px-6 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
                            Enable Two-Factor
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">QR Code</h2>
                <div class="flex justify-center mb-6">
                    <div class="bg-white rounded-xl p-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-48 h-48" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="w-48 h-48 items-center justify-center hidden">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-dark-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <p class="text-dark-600 text-xs">QR code unavailable</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-dark-400 text-xs">
                    Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)
                </p>
            </div>

            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">Manual Entry</h2>
                <p class="text-dark-400 text-sm mb-4">
                    If you can't scan the QR code, enter this key manually in your authenticator app:
                </p>
                <div class="glass-light rounded-xl p-4 text-center">
                    <code class="text-primary-400 font-mono text-lg tracking-wider">{{ $formattedSecret }}</code>
                </div>
                <p class="text-dark-500 text-xs mt-3 text-center">
                    Make sure to save this key. You'll need it if you lose access to your authenticator app.
                </p>
            </div>

            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-4">Supported Apps</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="glass-light rounded-xl p-3 text-center">
                        <p class="text-white text-sm font-medium">Google Authenticator</p>
                    </div>
                    <div class="glass-light rounded-xl p-3 text-center">
                        <p class="text-white text-sm font-medium">Authy</p>
                    </div>
                    <div class="glass-light rounded-xl p-3 text-center">
                        <p class="text-white text-sm font-medium">Microsoft Authenticator</p>
                    </div>
                    <div class="glass-light rounded-xl p-3 text-center">
                        <p class="text-white text-sm font-medium">1Password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('dashboard.profile') }}" class="inline-flex items-center gap-2 text-dark-400 hover:text-white text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Profile
        </a>
    </div>
</div>
@endsection
