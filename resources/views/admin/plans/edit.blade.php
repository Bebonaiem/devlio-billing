@extends('layouts.admin')

@section('title', 'Edit Plan')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Product</label>
                <select name="product_id" required class="w-full border rounded px-3 py-2">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ $plan->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name', $plan->name) }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description', $plan->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">CPU (%)</label>
                <input type="number" name="cpu" required value="{{ $plan->cpu }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Memory (MB)</label>
                <input type="number" name="memory" required value="{{ $plan->memory }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Disk (MB)</label>
                <input type="number" name="disk" required value="{{ $plan->disk }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Swap (MB)</label>
                <input type="number" name="swap" value="{{ $plan->swap }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Databases</label>
                <input type="number" name="databases" value="{{ $plan->databases }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Backups</label>
                <input type="number" name="backups" value="{{ $plan->backups }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Allocations</label>
                <input type="number" name="allocations" value="{{ $plan->allocations }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Billing Cycle</label>
                <select name="billing_cycle" required class="w-full border rounded px-3 py-2">
                    @foreach (['monthly', 'quarterly', 'semi_annually', 'annually'] as $cycle)
                        <option value="{{ $cycle }}" {{ $plan->billing_cycle == $cycle ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cycle)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Price ($)</label>
                <input type="number" name="price" required step="0.01" value="{{ $plan->price }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Setup Fee ($)</label>
                <input type="number" name="setup_fee" step="0.01" value="{{ $plan->setup_fee }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pterodactyl Nest ID</label>
                <input type="number" name="nest_id" value="{{ $plan->nest_id }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pterodactyl Egg ID</label>
                <input type="number" name="egg_id" value="{{ $plan->egg_id }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="mr-2">
            <span class="text-sm">Active</span>
        </label>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Plan</button>
    </form>
</div>
@endsection
