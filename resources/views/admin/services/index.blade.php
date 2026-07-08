@extends('layouts.admin')
@section('title', 'Services')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-display font-bold text-white">Services</h1>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20 mb-6">
            <p class="text-sm text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">ID</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">User</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Plan</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-dark-400 uppercase">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-medium text-dark-400 uppercase">Price</th>
                        <th class="text-right px-6 py-4 text-xs font-medium text-dark-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($services as $service)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4 text-sm text-dark-400">#{{ $service->id }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-white">{{ $service->user->name }}</span>
                                <p class="text-xs text-dark-500">{{ $service->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-white">{{ $service->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-white">{{ $service->plan->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400' : ($service->status === 'suspended' ? 'bg-red-500/10 text-red-400' : 'bg-dark-700 text-dark-400') }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-white text-right">${{ number_format($service->price, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.services.show', $service) }}" class="text-primary-400 hover:text-primary-300 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="text-dark-500">No services found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $services->links() }}</div>
</div>
@endsection
