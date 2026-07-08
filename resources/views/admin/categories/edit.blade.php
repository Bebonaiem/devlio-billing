@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name', $category->name) }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Slug</label>
            <input type="text" name="slug" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('slug', $category->slug) }}" placeholder="Leave blank to auto-generate">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Parent Category</label>
            <select name="parent_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                <option value="">— None (top level) —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description', $category->description) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Image URL</label>
            <input type="text" name="image" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('image', $category->image) }}">
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" {{ $category->enabled ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Enabled</span>
            </label>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-1">Sort Order</label>
                <input type="number" name="order" value="{{ old('order', $category->order) }}" class="w-20 px-3 py-2 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Category</button>
    </form>
</div>
@endsection