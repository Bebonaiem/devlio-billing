@extends('layouts.admin')
@section('title', 'Upgrade #' . $upgrade->id)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.upgrades.index') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Upgrades
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-display font-bold text-white">Upgrade #{{ $upgrade->id }}</h1>
                <p class="text-dark-400 mt-1">{{ $upgrade->service?->user?->name }} - {{ $upgrade->service?->product?->name ?? 'N/A' }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium
                {{ $upgrade->status === 'approved' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($upgrade->status === 'denied' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">
                {{ ucfirst($upgrade->status) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6">
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-red-500/20 mb-6">
            <p class="text-sm text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    <div class="space-y-6">
        <div class="glass rounded-2xl p-6">
            <h2 class="text-lg font-display font-bold text-white mb-4">Upgrade Details</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">User</label>
                    <p class="text-white font-medium">{{ $upgrade->service?->user?->name ?? 'N/A' }}</p>
                    <p class="text-xs text-dark-500">{{ $upgrade->service?->user?->email }}</p>
                </div>
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">Service</label>
                    <p class="text-white font-medium">{{ $upgrade->service?->product?->name ?? 'N/A' }}</p>
                    <p class="text-xs text-dark-500">Current Plan: {{ $upgrade->service?->plan?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">New Plan</label>
                    <p class="text-white font-medium">{{ $upgrade->plan?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">Product</label>
                    <p class="text-white font-medium">{{ $upgrade->product?->name ?? $upgrade->service?->product?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">Status</label>
                    <p class="text-white font-medium capitalize">{{ $upgrade->status }}</p>
                </div>
                <div>
                    <label class="text-xs text-dark-500 mb-1 block">Created</label>
                    <p class="text-white font-medium">{{ $upgrade->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        @if ($upgrade->invoice)
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-display font-bold text-white mb-4">Invoice</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white font-medium">Invoice #{{ $upgrade->invoice->id }}</p>
                        <p class="text-xs text-dark-500">
                            Status:
                            <span class="{{ $upgrade->invoice->status === 'paid' ? 'text-green-400' : ($upgrade->invoice->status === 'overdue' ? 'text-red-400' : 'text-yellow-400') }}">
                                {{ ucfirst($upgrade->invoice->status) }}
                            </span>
                        </p>
                    </div>
                    <a href="{{ route('admin.invoices.show', $upgrade->invoice) }}" class="text-sm text-primary-400 hover:text-primary-300">
                        View Invoice
                    </a>
                </div>
            </div>
        @endif

        @if ($upgrade->status === 'pending')
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-display font-bold text-white mb-4">Actions</h2>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.upgrades.approve', $upgrade) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-medium transition">
                            Approve & Apply
                        </button>
                    </form>
                    <form action="{{ route('admin.upgrades.deny', $upgrade) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg text-sm font-medium transition">
                            Deny
                        </button>
                    </form>
                    <form action="{{ route('admin.upgrades.destroy', $upgrade) }}" method="POST" onsubmit="return confirm('Delete this upgrade request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-dark-300 hover:text-white rounded-lg text-sm font-medium transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="glass rounded-2xl p-6">
                <form action="{{ route('admin.upgrades.destroy', $upgrade) }}" method="POST" onsubmit="return confirm('Delete this upgrade request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-dark-300 hover:text-white rounded-lg text-sm font-medium transition">
                        Delete Upgrade
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection