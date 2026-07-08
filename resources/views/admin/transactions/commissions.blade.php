@extends('layouts.admin')
@section('title', 'Commissions')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">Affiliate Commissions</h2>
</div>
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Affiliate</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Referred User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Rate</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th><th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th></tr></thead>
            <tbody>
                @foreach ($commissions as $c)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $c->affiliate->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-300">{{ $c->referredUser->name }}</td>
                        <td class="px-6 py-4 text-sm text-white">${{ number_format($c->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $c->rate }}%</td>
                        <td class="px-6 py-4"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $c->status === 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($c->status === 'approved' ? 'bg-primary-500/10 text-primary-400 border border-primary-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst($c->status) }}</span></td>
                        <td class="px-6 py-4 text-sm text-dark-400">{{ $c->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if ($c->status === 'pending')
                                    <form method="POST" action="{{ route('admin.commissions.approve', $c) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-primary-400 hover:text-primary-300 text-sm">Approve</button>
                                    </form>
                                @endif
                                @if ($c->status === 'approved')
                                    <form method="POST" action="{{ route('admin.commissions.pay', $c) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-400 hover:text-green-300 text-sm">Mark Paid</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $commissions->links() }}</div>
@endsection