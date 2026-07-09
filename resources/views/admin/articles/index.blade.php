@extends('layouts.admin')
@section('title', 'Articles')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">Articles</h2>
    <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Article
    </a>
</div>
<div class="glass rounded-2xl p-4 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..." class="w-full pl-10 pr-4 py-2.5 rounded-xl input-field text-white placeholder-dark-500 text-sm">
        </div>
        <button type="submit" class="px-4 py-2.5 bg-dark-700 hover:bg-dark-600 text-white text-sm rounded-xl transition">Search</button>
    </form>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Title</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Category</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Views</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Order</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($articles as $article)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="px-6 py-4 font-medium text-white text-sm">{{ $article->title }}</td>
                        <td class="px-6 py-4">
                            @if ($article->category)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    {{ $article->category }}
                                </span>
                            @else
                                <span class="text-dark-500 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.articles.toggle', $article) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $article->enabled ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-dark-500/10 text-dark-400 border border-dark-500/20' }}">
                                    {{ $article->enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $article->views }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $article->order_column }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.articles.edit', $article) }}" class="text-primary-400 hover:text-primary-300 text-sm transition">Edit</a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="inline" onsubmit="return confirm('Delete this article?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <p class="text-dark-500">No articles found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $articles->withQueryString()->links() }}</div>
@endsection
