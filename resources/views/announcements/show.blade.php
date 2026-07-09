@extends('layouts.dashboard')
@section('title', $announcement->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 animate-fade-in">
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 text-sm text-dark-400 hover:text-primary-400 transition mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Announcements
        </a>

        <div class="flex items-center gap-3 mb-4">
            @if ($announcement->category)
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">
                    {{ $announcement->category }}
                </span>
            @endif
            <span class="text-sm text-dark-500">{{ $announcement->created_at->format('F d, Y') }}</span>
        </div>

        <h1 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">{{ $announcement->title }}</h1>

        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white">
                {{ substr($announcement->author->name, 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-medium text-white">{{ $announcement->author->name }}</p>
                <p class="text-xs text-dark-500">Posted {{ $announcement->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    @if ($announcement->image)
        <div class="glass rounded-2xl overflow-hidden mb-8 animate-slide-up">
            <img src="{{ $announcement->image }}" alt="{{ $announcement->title }}" class="w-full object-cover max-h-96">
        </div>
    @endif

    <div class="glass rounded-2xl p-6 sm:p-8 animate-slide-up">
        <div class="prose prose-invert prose-lg max-w-none">
            {!! strip_tags($announcement->body, '<p><br><b><i><u><strong><em><ul><ol><li><a><h1><h2><h3><h4><span><blockquote><code><pre><img>') !!}
        </div>
    </div>
</div>
@endsection
