<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Game Server Billing')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-gray-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('storefront') }}" class="text-xl font-bold tracking-tight">
                    {{ config('app.name', 'GameBilling') }}
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('storefront') }}" class="hover:text-gray-300">Products</a>
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="hover:text-gray-300">Dashboard</a>
                        <a href="{{ route('dashboard.servers') }}" class="hover:text-gray-300">Servers</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hover:text-gray-300">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-gray-300">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        @if (session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-400 py-8 mt-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            &copy; {{ date('Y') }} {{ config('app.name', 'GameBilling') }}. All rights reserved.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
