@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md animate-fade-in">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center mb-4 animate-glow">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-white mb-2">Create an account</h1>
            <p class="text-dark-400 text-sm">Get started with high-performance game servers</p>
        </div>

        <div class="glass rounded-2xl p-8">
            <form method="POST" action="{{ url('/register') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-dark-300 mb-2">First name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                            class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                            placeholder="John" required autofocus>
                        @error('first_name')
                            <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-dark-300 mb-2">Last name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                            class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                            placeholder="Doe" required>
                        @error('last_name')
                            <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-dark-300 mb-2">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                        placeholder="you@example.com" required>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-dark-300 mb-2">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                        placeholder="Min. 8 characters" required>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-dark-300 mb-2">Confirm password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">
                    Create account
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/5 text-center">
                <p class="text-sm text-dark-400">
                    Already have an account?
                    <a href="{{ url('/login') }}" class="text-primary-400 hover:text-primary-300 font-medium transition">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
