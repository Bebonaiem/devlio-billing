@extends('layouts.dashboard')
@section('title', $article->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 animate-fade-in">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-sm text-dark-400 hover:text-primary-400 transition mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Help Center
        </a>

        <div class="flex items-center gap-3 mb-4">
            @if ($article->category)
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">
                    {{ $article->category }}
                </span>
            @endif
            <span class="text-sm text-dark-500">{{ $article->created_at->format('F d, Y') }}</span>
            <span class="text-sm text-dark-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                {{ $article->views }} views
            </span>
        </div>

        <h1 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">{{ $article->title }}</h1>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8 animate-slide-up">
        <div class="prose prose-invert prose-lg max-w-none">
            {!! strip_tags($article->body, '<p><br><b><i><u><strong><em><ul><ol><li><a><h1><h2><h3><h4><span><blockquote><code><pre><img>') !!}
        </div>
    </div>
</div>
@endsection
