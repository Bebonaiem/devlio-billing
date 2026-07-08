@extends('layouts.app')

@section('title', 'Order Cancelled')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 text-center">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-red-500 text-6xl mb-4">&#10007;</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Cancelled</h1>
        <p class="text-lg text-gray-600 mb-6">Your payment was cancelled. No charges have been made.</p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('checkout.index', $order->plan) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Try Again</a>
            <a href="{{ route('storefront') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
