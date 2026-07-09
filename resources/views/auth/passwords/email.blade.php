@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center mb-4 animate-glow">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">Reset your password</h1>
            <p class="text-dark-400 text-sm">Enter your email and we'll send you a reset link</p>
        </div>

        <div class="glass rounded-2xl p-8">
            @if (session('status'))
                <div class="mb-5 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-dark-300 mb-2">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                        placeholder="you@example.com" required autofocus>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">
                    Send Password Reset Link
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/5 text-center">
                <p class="text-sm text-dark-400">
                    Remember your password?
                    <a href="{{ url('/login') }}" class="text-primary-400 hover:text-primary-300 font-medium transition">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection