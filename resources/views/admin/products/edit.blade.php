@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name', $product->name) }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Slug (leave blank to auto-generate)</label>
            <input type="text" name="slug" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('slug', $product->slug) }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description', $product->description) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Category</label>
            <select name="category_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <option value="">None</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Image URL</label>
            <input type="text" name="image" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('image', $product->image) }}">
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Per-User Limit</label>
                <input type="number" name="per_user_limit" min="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" value="{{ old('per_user_limit', $product->per_user_limit) }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Stock</label>
                <input type="number" name="stock" min="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" value="{{ old('stock', $product->stock) }}">
            </div>
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" {{ $product->enabled ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Enabled</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="hidden" value="1" {{ $product->hidden ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Hidden</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="allow_quantity" value="1" {{ $product->allow_quantity ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Allow Quantity</span>
            </label>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.plans', $product) }}" class="flex-1 text-center py-3 px-4 bg-dark-800 hover:bg-dark-700 text-white font-medium rounded-xl text-sm transition">Manage Plans</a>
            <a href="{{ route('admin.products.categories') }}" class="flex-1 text-center py-3 px-4 bg-dark-800 hover:bg-dark-700 text-white font-medium rounded-xl text-sm transition">Manage Categories</a>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Product</button>
    </form>
</div>
@endsection