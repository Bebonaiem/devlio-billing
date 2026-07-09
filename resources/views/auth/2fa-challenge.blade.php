@extends('layouts.app')
@section('title', 'Two-Factor Authentication')
@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center mb-4 animate-glow">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">Two-factor authentication</h1>
            <p class="text-dark-400 text-sm">Enter the 6-digit code from your authenticator app</p>
        </div>

        <div class="glass rounded-2xl p-8">
            <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-dark-300 mb-2">Verification code</label>
                    <input type="text" name="code" id="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                        class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm text-center tracking-[0.5em]"
                        placeholder="000000" required autofocus>
                    @error('code')
                        <p class="mt-1.5 text-sm text-red-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">
                    Verify
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/5 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-dark-400 hover:text-primary-300 font-medium transition">
                        Cancel and log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
