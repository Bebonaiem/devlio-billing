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
                <label class="block text-sm font-medium text-dark-300 mb-2">Type</label>
                <select name="type" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="recurring" {{ $plan->type === 'recurring' ? 'selected' : '' }}>Recurring</option>
                    <option value="one-time" {{ $plan->type === 'one-time' ? 'selected' : '' }}>One-Time</option>
                    <option value="free" {{ $plan->type === 'free' ? 'selected' : '' }}>Free</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Period</label>
                <input type="number" name="billing_period" min="0" value="{{ old('billing_period', $plan->billing_period) }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Unit</label>
                <select name="billing_unit" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="day" {{ $plan->billing_unit === 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ $plan->billing_unit === 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ $plan->billing_unit === 'month' ? 'selected' : '' }}>Month</option>
                    <option value="year" {{ $plan->billing_unit === 'year' ? 'selected' : '' }}>Year</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Sort</label>
                <input type="number" name="sort" value="{{ old('sort', $plan->sort) }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
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
        <p class="text-xs text-dark-500">Prices are managed per currency in the prices section.</p>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Plan</button>
    </form>
</div>
@endsection
