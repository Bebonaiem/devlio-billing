@extends('layouts.app')

@section('title', 'Affiliate Program')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Affiliate Program</h1>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $referrals }}</p>
            <p class="text-gray-600">Referrals</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">${{ number_format($totalEarned, 2) }}</p>
            <p class="text-gray-600">Total Earned</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-3xl font-bold text-purple-600 font-mono text-sm">{{ $user->affiliate_code }}</p>
            <p class="text-gray-600">Your Referral Code</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Your Referral Link</h2>
        <div class="bg-gray-50 border rounded p-3 font-mono text-sm break-all">
            {{ url('/?ref=' . $user->affiliate_code) }}
        </div>
        <p class="text-sm text-gray-500 mt-2">Share this link with friends. You earn commission on their first purchase!</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Commission History</h2>
        @if ($commissions->isEmpty())
            <p class="text-gray-500">No commissions yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b text-left">
                        <th class="pb-2">Referred User</th>
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Rate</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commissions as $c)
                        <tr class="border-b">
                            <td class="py-2">{{ $c->referredUser->name }}</td>
                            <td class="py-2">${{ number_format($c->amount, 2) }}</td>
                            <td class="py-2">{{ $c->rate }}%</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-sm {{ $c->status === 'approved' ? 'bg-green-100 text-green-700' : ($c->status === 'paid' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="py-2">{{ $c->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $commissions->links() }}</div>
        @endif
    </div>
</div>
@endsection
