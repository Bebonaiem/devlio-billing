@extends('layouts.app')

@section('title', 'My Servers')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Servers</h1>

    @if ($servers->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-xl text-gray-500 mb-4">You don't have any servers yet.</p>
            <a href="{{ route('storefront') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Browse Products</a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach ($servers as $server)
                <div class="bg-white rounded-lg shadow p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $server->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $server->order->plan->product->name ?? 'N/A' }} - {{ $server->order->plan->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-400">{{ $server->cpu }}% CPU | {{ $server->memory }}MB RAM | {{ $server->disk }}MB Disk</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded text-sm font-semibold
                            {{ $server->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $server->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $server->status === 'installing' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $server->status === 'terminated' ? 'bg-gray-100 text-gray-500' : '' }}">
                            {{ ucfirst($server->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $servers->links() }}</div>
    @endif
</div>
@endsection
