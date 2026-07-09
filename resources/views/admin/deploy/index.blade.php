@extends('layouts.admin')
@section('title', 'Deploy')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Deploy Updates</h1>
        <span class="text-xs text-dark-500 bg-dark-800 px-3 py-1.5 rounded-lg">
            {{ exec('cd ' . base_path() . ' && git log --oneline -1 2>&1') ?: 'unknown' }}
        </span>
    </div>

    @php
        $gitStatus = trim(exec('cd ' . base_path() . ' && git status --short 2>&1'));
    @endphp

    @if ($gitStatus === '')
        <div class="glass rounded-2xl p-5 mb-6 border border-green-500/20 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-green-300">Working tree is clean.</p>
        </div>
    @else
        <div class="glass rounded-2xl p-5 mb-6 border border-yellow-500/20 flex items-center gap-3">
            <svg class="w-5 h-5 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div>
                <p class="text-sm text-yellow-300">You have uncommitted changes.</p>
                <pre class="text-xs text-yellow-500 mt-1">{{ $gitStatus }}</pre>
            </div>
        </div>
    @endif

    <div class="glass rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-display font-bold text-white mb-2">Deploy from GitHub</h2>
        <p class="text-sm text-dark-400 mb-5">Pulls the latest code from <code class="text-primary-400">main</code>, runs migrations, and fixes permissions.</p>

        <form action="{{ route('admin.deploy.run') }}" method="POST" onsubmit="return confirm('Pull latest code and run migrations?')">
            @csrf
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl text-sm font-medium transition">
                Run Deploy
            </button>
        </form>
    </div>

    @if ($deployResult)
        <div class="glass rounded-2xl overflow-hidden {{ $deployResult['status'] === 'success' ? 'border-green-500/20' : 'border-red-500/20' }}">
            <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-sm font-medium text-white">Deploy Output</h3>
                <span class="text-sm {{ $deployResult['status'] === 'success' ? 'text-green-400' : 'text-red-400' }}">
                    {{ $deployResult['message'] }}
                </span>
            </div>
            <pre class="p-6 text-xs text-dark-300 font-mono leading-relaxed overflow-x-auto max-h-96 overflow-y-auto whitespace-pre-wrap">{{ $deployResult['output'] }}</pre>
        </div>
    @endif
</div>
@endsection