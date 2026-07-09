<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Devlio Billing') }}</title>
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
        .animate-glow { animation: glow 3s ease-in-out infinite alternate; }
        @keyframes glow { from { box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); } to { box-shadow: 0 0 40px rgba(99, 102, 241, 0.4); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    </style>
</head>
<body class="bg-dark-950 text-white min-h-screen flex flex-col">
    <header class="w-full px-6 py-4">
        <nav class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-display font-bold gradient-text">{{ config('app.name', 'Devlio Billing') }}</span>
            </a>
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 btn-primary text-white text-sm font-medium rounded-xl">Dashboard</a>
                    @else
                        <a href="{{ url('/login') }}" class="px-5 py-2 text-dark-300 hover:text-white text-sm font-medium transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ url('/register') }}" class="px-5 py-2 btn-primary text-white text-sm font-medium rounded-xl">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="max-w-4xl mx-auto text-center relative">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
            </div>
            <div class="relative">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-500/10 border border-primary-500/20 text-primary-400 text-xs font-medium mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-400 animate-pulse"></span>
                    High-Performance Game Hosting
                </div>
                <h1 class="text-5xl sm:text-7xl font-display font-bold mb-6 leading-tight">
                    Run Your Game Servers<br>
                    <span class="gradient-text">At Full Speed</span>
                </h1>
                <p class="text-dark-400 text-lg sm:text-xl max-w-2xl mx-auto mb-10">
                    Blazing fast game servers with instant deployment, DDoS protection, and 24/7 monitoring. Built for gamers, by gamers.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('storefront') }}" class="px-8 py-3.5 btn-primary text-white font-semibold rounded-xl text-sm inline-flex items-center gap-2">
                        Browse Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    @guest
                        <a href="{{ url('/register') }}" class="px-8 py-3.5 bg-dark-800 hover:bg-dark-700 text-dark-300 font-semibold rounded-xl text-sm transition border border-white/5">
                            Create Account
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-white/5 py-6 px-6">
        <div class="max-w-7xl mx-auto text-center text-dark-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'Devlio Billing') }}. All rights reserved.
        </div>
    </footer>
</body>
</html>