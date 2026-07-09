@extends('layouts.admin')
@section('title', 'Edit Announcement')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Title</label>
            <input type="text" name="title" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('title', $announcement->title) }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Category</label>
                <input type="text" name="category" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('category', $announcement->category) }}" placeholder="e.g. Maintenance, Update">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Image URL</label>
                <input type="url" name="image" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('image', $announcement->image) }}" placeholder="https://...">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Body</label>
            <textarea name="body" rows="8" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm resize-y">{{ old('body', $announcement->body) }}</textarea>
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" {{ old('enabled', $announcement->enabled) ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Enabled (visible to public)</span>
            </label>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Announcement</button>
    </form>
</div>
@endsection
