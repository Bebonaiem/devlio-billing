@extends('layouts.admin')
@section('title', 'Edit Article')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.articles.update', $article) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Title</label>
            <input type="text" name="title" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('title', $article->title) }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Category</label>
                <input type="text" name="category" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('category', $article->category) }}" placeholder="e.g. Getting Started, FAQ">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Order</label>
                <input type="number" name="order_column" min="0" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('order_column', $article->order_column) }}">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Body</label>
            <textarea name="body" rows="12" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm resize-y">{{ old('body', $article->body) }}</textarea>
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" {{ old('enabled', $article->enabled) ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Enabled (visible to public)</span>
            </label>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Article</button>
    </form>
</div>
@endsection
