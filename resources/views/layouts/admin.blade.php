<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b' },
                        dark: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a', 950: '#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], display: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .gradient-text { background: linear-gradient(135deg, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); transition: all 0.3s; }
        .btn-primary:hover { box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3); }
        .input-field { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); outline: none; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.05); }
        .sidebar-link.active { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles
</head>
<body class="bg-dark-950 text-white min-h-screen flex">
    <aside id="sidebar" class="w-64 bg-dark-900/80 backdrop-blur-xl border-r border-white/5 min-h-screen flex-shrink-0 flex flex-col fixed lg:sticky top-0 h-screen z-40 transition-transform -translate-x-full lg:translate-x-0">
        <div class="p-5 border-b border-white/5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-display font-bold gradient-text text-sm">{{ config('app.name') }}</span>
                    <span class="block text-[10px] text-dark-500 font-medium uppercase tracking-wider">Admin Panel</span>
                </div>
            </a>
        </div>
        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
            </a>
            <a href="{{ route('admin.plans.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Plans
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Categories
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Coupons
            </a>
            <a href="{{ route('admin.currencies.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Currencies
            </a>
            <a href="{{ route('admin.services.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                Services
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Tickets
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.users') || request()->routeIs('admin.user-detail') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
            </a>
            <div class="border-t border-white/5 my-2"></div>
            <a href="{{ route('admin.invoices.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Invoices
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Transactions
            </a>
            <a href="{{ route('admin.commissions.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Commissions
            </a>
            <a href="{{ route('admin.upgrades.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.upgrades.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Upgrades
            </a>
            <a href="{{ route('admin.deploy.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.deploy.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Deploy
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Announcements
            </a>
            <a href="{{ route('admin.articles.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Articles
            </a>
            <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
            <div class="pt-3 mt-3 border-t border-white/5">
                <a href="{{ route('storefront') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Store
                </a>
                <a href="{{ route('dashboard.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-400 hover:text-white w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <div class="flex-1 min-h-screen">
        <header class="sticky top-0 z-30 bg-dark-950/80 backdrop-blur-xl border-b border-white/5 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 text-dark-400 hover:text-white rounded-lg hover:bg-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold font-display">@yield('title', 'Admin Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-dark-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </a>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center text-xs font-bold">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            @if (session('success'))
                <div class="mb-4 glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-green-300">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 glass rounded-xl px-5 py-4 flex items-center gap-3 border-red-500/20">
                    <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <p class="text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar')?.classList.toggle('-translate-x-full');
        });
    </script>
    @stack('scripts')
    @livewireScripts
</body>
</html>