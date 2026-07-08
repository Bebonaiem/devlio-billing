@extends('layouts.dashboard')
@section('title', 'Affiliate Program')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">Affiliate Program</h1>

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="glass rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-primary-400">{{ $referrals }}</p>
            <p class="text-dark-400 text-sm mt-1">Referrals</p>
        </div>
        <div class="glass rounded-xl p-5 text-center">
            <p class="text-3xl font-bold gradient-text">${{ number_format($totalEarned, 2) }}</p>
            <p class="text-dark-400 text-sm mt-1">Total Earned</p>
        </div>
        <div class="glass rounded-xl p-5 text-center">
            <p class="text-lg font-bold text-purple-400 font-mono">{{ $user->affiliate_code }}</p>
            <p class="text-dark-400 text-sm mt-1">Your Referral Code</p>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 sm:p-8 mb-6">
        <h2 class="text-lg font-display font-bold text-white mb-4">Your Referral Link</h2>
        <div class="bg-dark-800/50 border border-white/5 rounded-xl p-4 font-mono text-sm text-dark-300 break-all">
            {{ url('/?ref=' . $user->affiliate_code) }}
        </div>
        <p class="text-sm text-dark-500 mt-3">Share this link with friends. You earn commission on their first purchase!</p>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h2 class="text-lg font-display font-bold text-white">Commission History</h2>
        </div>
        @if ($commissions->isEmpty())
            <div class="p-12 text-center">
                <p class="text-dark-500">No commissions yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Referred User</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Rate</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commissions as $c)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4 text-sm text-white">{{ $c->referredUser->name }}</td>
                                <td class="px-6 py-4 text-sm text-white">${{ number_format($c->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $c->rate }}%</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $c->status === 'approved' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($c->status === 'paid' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-dark-400">{{ $c->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-white/5">{{ $commissions->links() }}</div>
        @endif
    </div>
</div>
@endsection