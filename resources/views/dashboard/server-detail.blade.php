@extends('layouts.app')

@section('title', $server->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard.servers') }}" class="text-blue-600 hover:underline">&larr; Back to Servers</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ $server->name }}</h1>
                <p class="text-gray-500">{{ $server->order->plan->product->name ?? 'N/A' }}</p>
            </div>
            <span class="px-4 py-2 rounded text-lg font-semibold
                {{ $server->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                {{ $server->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}
                {{ $server->status === 'installing' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                {{ ucfirst($server->status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold mb-3">Resources</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span>CPU</span><span>{{ $server->cpu }}%</span></div>
                        <div class="w-full bg-gray-200 rounded h-2"><div class="bg-blue-600 rounded h-2" style="width: {{ min($server->cpu, 100) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span>Memory</span><span>{{ $server->memory }} MB</span></div>
                        <div class="w-full bg-gray-200 rounded h-2"><div class="bg-green-600 rounded h-2" style="width: {{ min($server->memory / 100, 100) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span>Disk</span><span>{{ $server->disk }} MB</span></div>
                        <div class="w-full bg-gray-200 rounded h-2"><div class="bg-purple-600 rounded h-2" style="width: {{ min($server->disk / 1000, 100) }}%"></div></div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-3">Details</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-600">Plan:</dt><dd>{{ $server->order->plan->name ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Billing:</dt><dd class="capitalize">{{ str_replace('_', ' ', $server->order->plan->billing_cycle ?? '') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Price:</dt><dd>${{ number_format($server->order->plan->price ?? 0, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Node:</dt><dd>{{ $server->node ?? 'N/A' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Created:</dt><dd>{{ $server->created_at->format('M d, Y') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
