@extends('layouts.admin')
@section('title', 'Upgrades')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Upgrades</h1>
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

    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">ID</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">User</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Service</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">New Plan</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Invoice</th>
                        <th class="text-right px-6 py-4 text-xs font-medium text-dark-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($upgrades as $upgrade)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4 text-sm text-dark-400">#{{ $upgrade->id }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-white">{{ $upgrade->service?->user?->name ?? 'N/A' }}</span>
                                <p class="text-xs text-dark-500">{{ $upgrade->service?->user?->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-white">{{ $upgrade->service?->product?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-white">{{ $upgrade->plan?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ $upgrade->status === 'approved' ? 'bg-green-500/10 text-green-400' : ($upgrade->status === 'denied' ? 'bg-red-500/10 text-red-400' : 'bg-yellow-500/10 text-yellow-400') }}">
                                    {{ ucfirst($upgrade->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($upgrade->invoice)
                                    <a href="{{ route('admin.invoices.show', $upgrade->invoice) }}" class="text-primary-400 hover:text-primary-300 text-sm">
                                        #{{ $upgrade->invoice->id }}
                                    </a>
                                @else
                                    <span class="text-dark-500 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.upgrades.show', $upgrade) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <p class="text-dark-500">No upgrades found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $upgrades->links() }}</div>
</div>
@endsection