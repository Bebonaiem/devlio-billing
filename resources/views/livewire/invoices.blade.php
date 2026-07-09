<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Invoices</h1>
            <p class="text-dark-400 mt-1">View and pay your invoices</p>
        </div>

        <div class="flex gap-4 mb-6">
            <select wire:model.live="status" class="px-4 py-2.5 rounded-lg input-field text-white text-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        @if($invoices->isEmpty())
            <div class="glass rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-dark-400">No invoices found.</p>
            </div>
        @else
            <div class="glass rounded-xl overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-6 py-4 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Number</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-4 text-sm font-medium text-white">{{ $invoice->number }}</td>
                                <td class="px-6 py-4 text-sm text-dark-300">{{ $invoice->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-white">{{ $invoice->formatted_total }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ $invoice->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                        {{ $invoice->status === 'overdue' ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ $invoice->status === 'cancelled' ? 'bg-dark-500/20 text-dark-400' : '' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('dashboard.invoice-detail', $invoice->number) }}" class="px-3 py-1.5 text-xs text-dark-300 hover:text-white rounded-lg hover:bg-white/5">View</a>
                                        @if($invoice->status === 'pending')
                                            <button wire:click="payInvoice({{ $invoice->id }})" class="px-3 py-1.5 text-xs btn-primary text-white rounded-lg">Pay Now</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
