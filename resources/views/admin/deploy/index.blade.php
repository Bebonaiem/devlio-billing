@extends('layouts.admin')
@section('title', 'Deploy')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Deploy Updates</h1>
        <span class="text-xs text-dark-500 bg-dark-800 px-3 py-1.5 rounded-lg">
            {{ config('app.version', 'v1.0.0') ?? 'v1.0.0' }} · {{ exec('git log --oneline -1 2>&1') ?: 'unknown' }}
        </span>
    </div>

    @php
        $gitStatus = trim(exec('cd ' . base_path() . ' && git status --short 2>&1'));
        $gitBehind = trim(exec('cd ' . base_path() . ' && git rev-list --count HEAD..origin/main 2>&1'));
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

        <div class="flex items-center gap-3">
            <button id="deployBtn"
                    data-url="{{ route('admin.deploy.run') }}"
                    class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-sm font-medium transition flex items-center gap-2">
                <svg id="deploySpinner" class="w-4 h-4 hidden animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span id="deployText">Run Deploy</span>
            </button>
            <span id="deployStatus" class="text-sm text-dark-400 hidden"></span>
        </div>
    </div>

    <div id="outputCard" class="glass rounded-2xl overflow-hidden hidden">
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-medium text-white">Deploy Output</h3>
        </div>
        <pre id="deployOutput" class="p-6 text-xs text-dark-300 font-mono leading-relaxed overflow-x-auto max-h-96 overflow-y-auto whitespace-pre-wrap"></pre>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('deployBtn')?.addEventListener('click', function() {
    const btn = this;
    const spinner = document.getElementById('deploySpinner');
    const text = document.getElementById('deployText');
    const status = document.getElementById('deployStatus');
    const outputCard = document.getElementById('outputCard');
    const output = document.getElementById('deployOutput');

    btn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = 'Deploying...';
    status.classList.remove('hidden');
    status.className = 'text-sm text-dark-400';
    status.textContent = 'Running...';
    outputCard.classList.remove('hidden');
    output.textContent = '';

    fetch(btn.dataset.deployUrl, { method: 'POST', headers: { 'Accept': 'application/json' } })
        .then(r => r.text().then(text => { try { return JSON.parse(text); } catch(e) { throw new Error(text.substring(0, 200)); } }))
        .then(data => {
            output.textContent = data.output;
            if (data.status === 'success') {
                status.className = 'text-sm text-green-400';
                status.textContent = 'Success: ' + data.message;
            } else {
                status.className = 'text-sm text-red-400';
                status.textContent = 'Error: ' + data.message;
            }
        })
        .catch(err => {
            output.textContent = 'Network error: ' + err.message;
            status.className = 'text-sm text-red-400';
            status.textContent = 'Error: Request failed';
        })
        .finally(() => {
            spinner.classList.add('hidden');
            text.textContent = 'Run Deploy';
            btn.disabled = false;
        });
});
</script>
@endpush
@endsection