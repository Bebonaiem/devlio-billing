@extends('layouts.admin')
@section('title', 'Create Plan')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Product</label>
                <select name="product_id" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name') }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">CPU (%)</label>
                <input type="number" name="cpu" required value="100" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Memory (MB)</label>
                <input type="number" name="memory" required value="1024" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Disk (MB)</label>
                <input type="number" name="disk" required value="10240" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Swap (MB)</label>
                <input type="number" name="swap" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Databases</label>
                <input type="number" name="databases" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Backups</label>
                <input type="number" name="backups" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Allocations</label>
                <input type="number" name="allocations" value="1" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Cycle</label>
                <select name="billing_cycle" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semi_annually">Semi-Annually</option>
                    <option value="annually">Annually</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Price ($)</label>
                <input type="number" name="price" required step="0.01" value="0.00" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Setup Fee ($)</label>
                <input type="number" name="setup_fee" step="0.01" value="0.00" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Pterodactyl Nest ID</label>
                <input type="number" name="nest_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Pterodactyl Egg ID</label>
                <input type="number" name="egg_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
            <span class="text-sm text-dark-300">Active</span>
        </label>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Create Plan</button>
    </form>
</div>
@endsection