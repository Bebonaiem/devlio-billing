<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-white min-h-screen flex-shrink-0">
        <div class="p-4 text-xl font-bold border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}">{{ config('app.name') }} Admin</a>
        </div>
        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Products</a>
            <a href="{{ route('admin.plans.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Plans</a>
            <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Orders</a>
            <a href="{{ route('admin.users') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Users</a>
            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Settings</a>
            <hr class="border-gray-700 my-4">
            <a href="{{ route('storefront') }}" class="block px-4 py-2 rounded hover:bg-gray-700">View Store</a>
            <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700">My Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700">Logout</button>
            </form>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-2xl font-bold">@yield('title', 'Admin Dashboard')</h1>
        </header>

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
