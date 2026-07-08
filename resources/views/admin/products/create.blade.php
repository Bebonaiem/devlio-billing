@extends('layouts.admin')
@section('title', 'Create Product')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.products.store') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name') }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Image URL</label>
            <input type="text" name="image" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('image') }}">
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" checked class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Active</span>
            </label>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="w-20 px-3 py-2 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Create Product</button>
    </form>
</div>
@endsection