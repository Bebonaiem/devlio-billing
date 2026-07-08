@extends('layouts.admin')

@section('title', 'Create Plan')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Product</label>
                <select name="product_id" required class="w-full border rounded px-3 py-2">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">CPU (%)</label>
                <input type="number" name="cpu" required value="100" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Memory (MB)</label>
                <input type="number" name="memory" required value="1024" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Disk (MB)</label>
                <input type="number" name="disk" required value="10240" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Swap (MB)</label>
                <input type="number" name="swap" value="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Databases</label>
                <input type="number" name="databases" value="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Backups</label>
                <input type="number" name="backups" value="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Allocations</label>
                <input type="number" name="allocations" value="1" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Billing Cycle</label>
                <select name="billing_cycle" required class="w-full border rounded px-3 py-2">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semi_annually">Semi-Annually</option>
                    <option value="annually">Annually</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Price ($)</label>
                <input type="number" name="price" required step="0.01" value="0.00" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Setup Fee ($)</label>
                <input type="number" name="setup_fee" step="0.01" value="0.00" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pterodactyl Nest ID</label>
                <input type="number" name="nest_id" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Pterodactyl Egg ID</label>
                <input type="number" name="egg_id" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" checked class="mr-2">
            <span class="text-sm">Active</span>
        </label>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Plan</button>
    </form>
</div>
@endsection
