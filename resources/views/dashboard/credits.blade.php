@extends('layouts.dashboard')
@section('title', 'Credits')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-display font-bold text-white mb-8">Credit Balance & Top-Up</h1>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-display font-bold text-white mb-6">Add Credits</h2>
                <form method="POST" action="{{ route('dashboard.credits.process') }}" class="space-y-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1.5">Currency</label>
                            <select name="currency" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white">
                                @foreach ($currencies as $curr)
                                    <option value="{{ $curr->code }}" {{ $curr->code === $currencyCode ? 'selected' : '' }}>{{ $curr->name }} ({{ $curr->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1.5">Amount (min $1.00)</label>
                            <input type="number" name="amount" min="1" max="10000" step="0.01" value="{{ old('amount', '10') }}" class="w-full px-4 py-2.5 input-field rounded-xl text-sm text-white placeholder-dark-500" required>
                            @error('amount') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="text-xs text-dark-500">You will be redirected to the payment page to complete the deposit.</p>
                    <button type="submit" class="px-6 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">Deposit Credits</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="glass rounded-2xl p-6">
                <h3 class="font-display font-bold text-white mb-4">Current Balance</h3>
                <div class="text-center py-4">
                    <span class="text-3xl font-bold gradient-text">${{ number_format($creditBalance, 2) }}</span>
                    <p class="text-dark-400 text-sm mt-1">{{ $currencyCode }}</p>
                </div>
                <dl class="space-y-2 mt-4">
                    <div class="flex justify-between py-2 border-t border-white/5">
                        <dt class="text-dark-400 text-sm">Minimum Deposit</dt>
                        <dd class="text-white text-sm">$1.00</dd>
                    </div>
                    <div class="flex justify-between py-2 border-t border-white/5">
                        <dt class="text-dark-400 text-sm">Maximum Deposit</dt>
                        <dd class="text-white text-sm">$10,000.00</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
