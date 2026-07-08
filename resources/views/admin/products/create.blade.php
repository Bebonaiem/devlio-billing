@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.products.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Image URL</label>
            <input type="text" name="image" class="w-full border rounded px-3 py-2" value="{{ old('image') }}">
        </div>
        <div class="flex gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                <span class="text-sm">Active</span>
            </label>
            <div>
                <label class="block text-sm font-medium mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="w-20 border rounded px-3 py-2">
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Product</button>
    </form>
</div>
@endsection
