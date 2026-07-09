@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-3xl md:text-4xl font-display font-bold gradient-text mb-4">Announcements</h1>
        <p class="text-dark-400 text-lg">Stay up to date with our latest news and updates.</p>
    </div>

    @if ($announcements->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($announcements as $announcement)
                <a href="{{ route('announcements.show', $announcement->slug) }}" class="block glass rounded-2xl overflow-hidden card-hover group">
                    @if ($announcement->image)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $announcement->image }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br from-primary-500/20 to-purple-500/20 flex items-center justify-center">
                            <svg class="w-12 h-12 text-primary-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            @if ($announcement->category)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">
                                    {{ $announcement->category }}
                                </span>
                            @endif
                            <span class="text-xs text-dark-500">{{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-white group-hover:text-primary-400 transition mb-2">{{ $announcement->title }}</h3>
                        <p class="text-dark-400 text-sm line-clamp-3">{{ strip_tags(Str::limit($announcement->body, 150)) }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12">{{ $announcements->links() }}</div>
    @else
        <div class="glass rounded-2xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-dark-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <h3 class="text-lg font-semibold text-white mb-2">No Announcements</h3>
            <p class="text-dark-400">There are no announcements at this time. Check back later!</p>
        </div>
    @endif
</div>
@endsection
