@extends('layouts.admin')
@section('title', 'Create Coupon')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.coupons.store') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Code</label>
            <input type="text" name="code" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('code') }}" placeholder="e.g. SUMMER50">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Type</label>
                <select name="type" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Value</label>
                <input type="number" name="value" step="0.01" min="0" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('value', '0') }}">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Max Uses</label>
                <input type="number" name="max_uses" min="0" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('max_uses') }}" placeholder="Leave blank for unlimited">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Max Per User</label>
                <input type="number" name="max_uses_per_user" min="0" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('max_uses_per_user') }}" placeholder="Leave blank for unlimited">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Starts At</label>
                <input type="datetime-local" name="starts_at" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" value="{{ old('starts_at') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Expires At</label>
                <input type="datetime-local" name="expires_at" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" value="{{ old('expires_at') }}">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Applies To</label>
            <select name="applies_to" id="applies_to" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <option value="all" {{ old('applies_to') === 'all' ? 'selected' : '' }}>All Products</option>
                <option value="specific" {{ old('applies_to') === 'specific' ? 'selected' : '' }}>Specific Products</option>
            </select>
        </div>
        <div id="products_section" class="{{ old('applies_to') === 'specific' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-dark-300 mb-2">Products</label>
            <select name="products[]" multiple class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" size="5">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" {{ in_array($product->id, old('products', [])) ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="recurring" value="1" {{ old('recurring') ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Recurring (applies to renewals)</span>
            </label>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Create Coupon</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('applies_to')?.addEventListener('change', function() {
    document.getElementById('products_section').classList.toggle('hidden', this.value !== 'specific');
});
</script>
@endpush