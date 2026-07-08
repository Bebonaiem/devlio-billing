@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Profile</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Account Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-gray-600">Name</dt>
                <dd>{{ $user->name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Email</dt>
                <dd>{{ $user->email }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Credit Balance</dt>
                <dd class="font-semibold text-green-600">${{ number_format($user->credit_balance, 2) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Affiliate Code</dt>
                <dd class="font-mono text-blue-600">{{ $user->affiliate_code }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Member Since</dt>
                <dd>{{ $user->created_at->format('F d, Y') }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Payment Methods</h2>
        @if ($paymentMethods->isEmpty())
            <p class="text-gray-500">No payment methods saved.</p>
        @else
            @foreach ($paymentMethods as $pm)
                <div class="border rounded p-3 mb-2 flex justify-between items-center">
                    <div>
                        <span class="capitalize font-medium">{{ $pm->gateway }}</span>
                        @if ($pm->last_four)
                            <span class="text-gray-500">**** {{ $pm->last_four }}</span>
                        @endif
                        @if ($pm->is_default)
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded ml-2">Default</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
