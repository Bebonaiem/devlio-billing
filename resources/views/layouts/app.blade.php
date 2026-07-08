<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Game Server Billing')</title>
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
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        glow: { '0%': { boxShadow: '0 0 5px rgba(99, 102, 241, 0.2)' }, '100%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.4)' } },
                    }
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
        .glass-light { background: rgba(30, 41, 59, 0.5); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.06); }
        .gradient-text { background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-border { position: relative; }
        .gradient-border::before { content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1px; background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none; }
        .btn-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3); }
        .btn-ghost { border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.2); }
        .stat-card { background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.7) 100%); border: 1px solid rgba(255, 255, 255, 0.06); }
        .nav-glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.06); }
        .input-field { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .input-field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); outline: none; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
        .animate-delay-100 { animation-delay: 100ms; }
        .animate-delay-200 { animation-delay: 200ms; }
        .animate-delay-300 { animation-delay: 300ms; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    @livewireStyles
</head>
<body class="bg-dark-950 text-white min-h-screen flex flex-col">
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-pink-500/5 rounded-full blur-3xl"></div>
    </div>

    <nav class="nav-glass fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('storefront') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl font-bold gradient-text">{{ config('app.name', 'Devlio') }}</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('storefront') }}" class="px-4 py-2 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5 transition-all">Products</a>
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="px-4 py-2 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5 transition-all">Dashboard</a>
                        <a href="{{ route('dashboard.servers') }}" class="px-4 py-2 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5 transition-all">Servers</a>
                        <div class="w-px h-6 bg-white/10 mx-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm text-dark-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5 transition-all">Login</a>
                        <a href="{{ route('register') }}" class="ml-2 px-5 py-2 text-sm font-medium btn-primary text-white rounded-lg">Get Started</a>
                    @endauth
                </div>

                <button id="mobileMenuBtn" class="md:hidden p-2 text-dark-300 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="hidden md:hidden border-t border-white/5">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('storefront') }}" class="block px-4 py-2.5 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5">Products</a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="block px-4 py-2.5 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5">Dashboard</a>
                    <a href="{{ route('dashboard.servers') }}" class="block px-4 py-2.5 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5">Servers</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-dark-400 hover:text-white rounded-lg hover:bg-white/5">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm text-dark-300 hover:text-white rounded-lg hover:bg-white/5">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm font-medium text-primary-400 hover:text-primary-300 rounded-lg hover:bg-primary-500/10">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-1 pt-16 relative z-10">
        @if (session('success'))
            <div class="max-w-7xl mx-auto mt-6 px-4 animate-fade-in">
                <div class="glass rounded-xl px-5 py-4 flex items-center gap-3 border-green-500/20">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-7xl mx-auto mt-6 px-4 animate-fade-in">
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

    <footer class="relative z-10 border-t border-white/5 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold gradient-text">{{ config('app.name', 'Devlio') }}</span>
                </div>
                <p class="text-sm text-dark-500">&copy; {{ date('Y') }} {{ config('app.name', 'Devlio') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
            document.getElementById('mobileMenu')?.classList.toggle('hidden');
        });
    </script>
    @livewireScripts
</body>
</html>