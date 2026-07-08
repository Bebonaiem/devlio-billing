<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
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
                    animation: { 'fade-in': 'fadeIn 0.5s ease-out', 'slide-up': 'slideUp 0.5s ease-out', 'glow': 'glow 2s ease-in-out infinite alternate' },
                    keyframes: { fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } }, slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } }, glow: { '0%': { boxShadow: '0 0 5px rgba(99, 102, 241, 0.2)' }, '100%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.4)' } } }
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
        .glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .glass-light { background: rgba(30, 41, 59, 0.5); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.06); }
        .gradient-text { background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3); }
        .btn-ghost { border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.2); }
        .nav-glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.06); }
        .input-field { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); outline: none; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.05); }
        .sidebar-link.active { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    @livewireStyles
</head>
<body class="bg-dark-950 text-white min-h-screen flex flex-col">
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-pink-500/5 rounded-full blur-3xl"></div>
    </div>

    <nav class="nav-glass fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" class="lg:hidden p-2 text-dark-400 hover:text-white rounded-lg hover:bg-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('storefront') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="font-display text-xl font-bold gradient-text">{{ config('app.name', 'Devlio') }}</span>
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('storefront') }}" class="hidden sm:inline-flex px-3 py-1.5 text-xs text-dark-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">Store</a>
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-dark-400 hover:text-white rounded-lg hover:bg-white/5 transition-all" x-data="{ cartCount: {{ count(session('cart', [])) }} }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        <span x-show="cartCount > 0" x-cloak class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-primary-500 text-[10px] font-bold text-white flex items-center justify-center" x-text="cartCount"></span>
                    </a>
                    @can('admin')
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex px-3 py-1.5 text-xs text-primary-400 hover:text-primary-300 rounded-lg hover:bg-primary-500/10 transition-all">Admin</a>
                    @endcan
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 px-3 py-1.5 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5 transition-all">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center text-[10px] font-bold text-white">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 glass rounded-xl border border-white/5 shadow-xl overflow-hidden z-50">
                            <div class="p-2 space-y-0.5">
                                <a href="{{ route('dashboard.profile') }}" class="block px-3 py-2 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5">{{ auth()->user()->name }}</a>
                                <div class="border-t border-white/5 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-red-400 hover:text-red-300 rounded-lg hover:bg-red-500/10 w-full">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-1 pt-16 relative z-10">
        <aside id="sidebar" class="w-64 bg-dark-900/80 backdrop-blur-xl border-r border-white/5 min-h-[calc(100vh-4rem)] flex-shrink-0 flex flex-col fixed lg:sticky top-16 h-[calc(100vh-4rem)] z-40 transition-transform -translate-x-full lg:translate-x-0">
            <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('dashboard.servers') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.servers') || request()->routeIs('dashboard.server-detail') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    Servers
                </a>
                <a href="{{ route('dashboard.invoices') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.invoices') || request()->routeIs('dashboard.invoice-detail') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Invoices
                </a>
                <a href="{{ route('dashboard.tickets') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.tickets') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.5-1 2.5-2 3.5-1 1-2 2-2 4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Support
                </a>
                <a href="{{ route('dashboard.affiliate') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.affiliate') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Affiliate
                </a>
                <a href="{{ route('dashboard.profile') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-300 hover:text-white {{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </a>
                <div class="pt-3 mt-3 border-t border-white/5">
                    <a href="{{ route('storefront') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-dark-400 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Browse Store
                    </a>
                    @can('admin')
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-primary-400 hover:text-primary-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016a11.955 11.955 0 01-2.667 1.048M12 6.804a5.974 5.974 0 01-2.128 1.036M4.4 5.398c.004.128.006.256.006.384A6.301 6.301 0 006 11.5a6.193 6.193 0 01-1.893.434"/></svg>
                            Admin Panel
                        </a>
                    @endcan
                </div>
            </nav>
        </aside>

        <main class="flex-1 min-h-[calc(100vh-4rem)]">
            @if (session('success'))
                <div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8 animate-fade-in">
                    <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8 animate-fade-in">
                    <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-red-500/20">
                        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <p class="text-sm text-red-300">{{ session('error') }}</p>
                    </div>
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
    @livewireScripts
</body>
</html>