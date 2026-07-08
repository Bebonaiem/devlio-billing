@extends('layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 text-center">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-green-500 text-6xl mb-4">&#10003;</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
        <p class="text-lg text-gray-600 mb-6">Your order has been placed. Your server will be provisioned shortly.</p>
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Product:</strong> {{ $order->plan->product->name }} - {{ $order->plan->name }}</p>
            <p><strong>Status:</strong> <span class="text-yellow-600 font-semibold">Pending Setup</span></p>
        </div>
        <div class="flex justify-center gap-4">
            <a href="{{ route('dashboard.servers') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">View My Servers</a>
            <a href="{{ route('storefront') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
