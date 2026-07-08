@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-xl p-5 hover:border-primary-500/30 transition-all">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-primary-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <span class="text-dark-400 text-sm">Total Users</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
    </div>
    <div class="glass rounded-xl p-5 hover:border-green-500/30 transition-all">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-dark-400 text-sm">Active Orders</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['active_services'] }}</p>
    </div>
    <div class="glass rounded-xl p-5 hover:border-yellow-500/30 transition-all">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-dark-400 text-sm">Pending Invoices</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['pending_invoices'] }}</p>
    </div>
    <div class="glass rounded-xl p-5 hover:border-purple-500/30 transition-all">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <span class="text-dark-400 text-sm">Revenue (Month)</span>
        </div>
        <p class="text-3xl font-bold gradient-text">${{ number_format($stats['monthly_revenue'], 2) }}</p>
    </div>
</div>

<div class="glass rounded-2xl p-6 mb-8">
    <h2 class="text-lg font-display font-bold text-white mb-4">Revenue Overview</h2>
    <div class="relative h-64">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h2 class="text-lg font-display font-bold text-white">Recent Orders</h2>
        </div>
        @if ($recentServices->isEmpty())
            <div class="p-8 text-center"><p class="text-dark-500">No services yet.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">ID</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Product</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th></tr></thead>
                    <tbody>
                        @foreach ($recentServices as $service)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                                <td class="px-6 py-3 text-sm text-white">#{{ $service->id }}</td>
                                <td class="px-6 py-3 text-sm text-dark-300">{{ $service->user->name }}</td>
                                <td class="px-6 py-3 text-sm text-dark-300">{{ $service->product->name ?? ($service->plan->product->name ?? 'N/A') }}</td>
                                <td class="px-6 py-3"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $service->status === 'active' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : ($service->status === 'suspended' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20') }}">{{ ucfirst($service->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5">
            <h2 class="text-lg font-display font-bold text-white">Recent Transactions</h2>
        </div>
        @if ($recentTransactions->isEmpty())
            <div class="p-8 text-center"><p class="text-dark-500">No transactions yet.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="border-b border-white/5"><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">User</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Gateway</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Amount</th><th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th></tr></thead>
                    <tbody>
                        @foreach ($recentTransactions as $txn)
                            <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                                <td class="px-6 py-3 text-sm text-dark-300">{{ $txn->invoice->user->name ?? $txn->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm text-dark-300 capitalize">{{ $txn->gateway->name ?? $txn->gateway }}</td>
                                <td class="px-6 py-3 text-sm text-white">${{ number_format($txn->amount, 2) }}</td>
                                <td class="px-6 py-3 text-sm text-dark-400">{{ $txn->created_at->format('M d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueLabels) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueData) !!},
                    borderColor: '#818cf8',
                    backgroundColor: 'rgba(129, 140, 248, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#818cf8',
                    pointBorderColor: '#818cf8',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#64748b', font: { size: 11 } },
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#64748b', font: { size: 11 }, callback: function(value) { return '$' + value.toLocaleString(); } },
                    }
                }
            }
        });
    }
});
</script>
@endsection
