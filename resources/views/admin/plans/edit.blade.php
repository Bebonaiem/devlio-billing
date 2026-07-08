@extends('layouts.admin')
@section('title', 'Edit Plan')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Product</label>
                <select name="product_id" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ $plan->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name', $plan->name) }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description', $plan->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">CPU (%)</label>
                <input type="number" name="cpu" required value="{{ $plan->cpu }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Memory (MB)</label>
                <input type="number" name="memory" required value="{{ $plan->memory }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Disk (MB)</label>
                <input type="number" name="disk" required value="{{ $plan->disk }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Swap (MB)</label>
                <input type="number" name="swap" value="{{ $plan->swap }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Databases</label>
                <input type="number" name="databases" value="{{ $plan->databases }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Backups</label>
                <input type="number" name="backups" value="{{ $plan->backups }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Allocations</label>
                <input type="number" name="allocations" value="{{ $plan->allocations }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Cycle</label>
                <select name="billing_cycle" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    @foreach (['monthly', 'quarterly', 'semi_annually', 'annually'] as $cycle)
                        <option value="{{ $cycle }}" {{ $plan->billing_cycle == $cycle ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cycle)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Price ($)</label>
                <input type="number" name="price" required step="0.01" value="{{ $plan->price }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Setup Fee ($)</label>
                <input type="number" name="setup_fee" step="0.01" value="{{ $plan->setup_fee }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Pterodactyl Nest ID</label>
                <input type="number" name="nest_id" value="{{ $plan->nest_id }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Pterodactyl Egg ID</label>
                <input type="number" name="egg_id" value="{{ $plan->egg_id }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
            <span class="text-sm text-dark-300">Active</span>
        </label>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Plan</button>
    </form>
</div>
@endsection