@extends('layouts.admin')

@section('title', 'Plans')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold">All Plans</h2>
    <a href="{{ route('admin.plans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Plan</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Product</th>
                <th class="px-6 py-3">Price</th>
                <th class="px-6 py-3">Cycle</th>
                <th class="px-6 py-3">Resources</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr class="border-t">
                    <td class="px-6 py-4 font-medium">{{ $plan->name }}</td>
                    <td class="px-6 py-4">{{ $plan->product->name }}</td>
                    <td class="px-6 py-4">${{ number_format($plan->price, 2) }}</td>
                    <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $plan->billing_cycle) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $plan->cpu }}% / {{ $plan->memory }}MB / {{ $plan->disk }}MB</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-sm {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
