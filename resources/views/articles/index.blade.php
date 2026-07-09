@extends('layouts.app')
@section('title', 'Help Center')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-3xl md:text-4xl font-display font-bold gradient-text mb-4">Help Center</h1>
        <p class="text-dark-400 text-lg">Find answers to your questions in our knowledge base.</p>
    </div>

    <div class="max-w-2xl mx-auto mb-10" x-data="{ search: '' }">
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Search articles..." class="w-full pl-12 pr-4 py-3.5 rounded-xl input-field text-white placeholder-dark-500 text-sm">
        </div>
    </div>

    @if ($articles->count())
        @foreach ($articles as $category => $categoryArticles)
            <div class="mb-10 animate-fade-in" x-show="!search || @js($categoryArticles->pluck('title')->implode(' ')).toLowerCase().includes(search.toLowerCase()) || '{{ strtolower($category ?: '') }}'.includes(search.toLowerCase())">
                <h2 class="text-xl font-display font-bold text-white mb-4 flex items-center gap-2">
                    @if ($category)
                        <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                        {{ $category }}
                    @else
                        <span class="w-2 h-2 rounded-full bg-dark-500"></span>
                        General
                    @endif
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($categoryArticles as $article)
                        <a href="{{ route('articles.show', $article->slug) }}" class="block glass rounded-xl p-5 card-hover group" x-show="!search || '{{ strtolower($article->title) }}'.includes(search.toLowerCase())">
                            <h3 class="text-white font-medium group-hover:text-primary-400 transition mb-2">{{ $article->title }}</h3>
                            <p class="text-dark-400 text-sm line-clamp-2">{{ strip_tags(Str::limit($article->body, 120)) }}</p>
                            <div class="flex items-center gap-3 mt-3 text-xs text-dark-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ $article->views }} views
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="glass rounded-2xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-dark-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="text-lg font-semibold text-white mb-2">No Articles Available</h3>
            <p class="text-dark-400">Our knowledge base is being set up. Check back soon!</p>
        </div>
    @endif
</div>
@endsection
