@extends('layouts.app')
@section('title', 'Order Successful')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
    <div class="glass rounded-2xl p-8 sm:p-10">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-green-500/20 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-display font-bold text-white mb-3">Payment Successful!</h1>
        <p class="text-dark-400 mb-8">Your order has been placed. Your server will be provisioned shortly.</p>

        <div class="glass rounded-xl p-5 mb-8 text-left">
            <div class="space-y-2">
                <div class="flex justify-between"><span class="text-dark-400 text-sm">Invoice ID</span><span class="text-white text-sm font-medium">#{{ $invoice->id }}</span></div>
                <div class="flex justify-between"><span class="text-dark-400 text-sm">Status</span><span class="text-yellow-400 text-sm font-semibold">Processing</span></div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ route('dashboard.services') }}" class="px-6 py-3 btn-primary text-white font-medium rounded-xl text-sm">View My Services</a>
            <a href="{{ route('storefront') }}" class="px-6 py-3 bg-dark-700 hover:bg-dark-600 text-dark-300 font-medium rounded-xl text-sm transition">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection