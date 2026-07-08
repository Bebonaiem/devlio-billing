@extends('layouts.admin')
@section('title', 'Plans')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">All Plans</h2>
    <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Plan
    </a>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Product</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Price</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Cycle</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Resources</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                        <td class="px-6 py-4 font-medium text-white text-sm">{{ $plan->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $plan->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-white">${{ number_format($plan->price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400 capitalize">{{ str_replace('_', ' ', $plan->billing_cycle) }}</td>
                        <td class="px-6 py-4 text-xs text-dark-400">{{ $plan->cpu }}% / {{ $plan->memory }}MB / {{ $plan->disk }}MB</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-dark-500/10 text-dark-400 border border-dark-500/20' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="text-primary-400 hover:text-primary-300 text-sm transition">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection