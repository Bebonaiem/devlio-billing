@extends('layouts.dashboard')
@section('title', 'Order Cancelled')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
    <div class="glass rounded-2xl p-8 sm:p-10">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-red-500/20 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h1 class="text-3xl font-display font-bold text-white mb-3">Payment Cancelled</h1>
        <p class="text-dark-400 mb-8">Your payment was cancelled. No charges have been made.</p>

        <div class="flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ route('checkout.index') }}" class="px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">Try Again</a>
            <a href="{{ route('storefront') }}" class="px-6 py-3 bg-dark-700 hover:bg-dark-600 text-dark-300 font-medium rounded-xl text-sm transition">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
