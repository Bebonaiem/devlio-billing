<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-display font-bold text-white">Credits</h1>
            <p class="text-dark-400 mt-1">Manage your credit balance</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="glass rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Current Balance</h2>
                <p class="text-3xl font-bold gradient-text">${{ number_format($balance, 2) }}</p>
                <p class="text-sm text-dark-400 mt-1">{{ $currencyCode }}</p>
            </div>

            <div class="glass rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Deposit Credits</h2>
                <div class="flex gap-4">
                    <select wire:model="currencyCode" class="px-4 py-2.5 rounded-lg input-field text-white text-sm">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" wire:model="depositAmount" min="1" step="0.01" class="flex-1 px-4 py-2.5 rounded-lg input-field text-white text-sm placeholder-dark-500" placeholder="Amount">
                    <button wire:click="deposit" class="px-6 py-2.5 btn-primary text-white rounded-lg text-sm font-medium">Deposit</button>
                </div>
            </div>
        </div>
    </div>
</div>
