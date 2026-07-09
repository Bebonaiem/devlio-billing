<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome to {{ $appName }}!</h1>
            </div>

            <div class="text-center mb-8">
                <p class="text-gray-600">Hi {{ $user->first_name ?? $user->name }},</p>
                <p class="text-gray-800 text-lg mt-2">Welcome! Your account is ready.</p>
                <p class="text-gray-600 mt-2">You can now browse our products, place orders, and manage your services from your dashboard.</p>
            </div>

            <div class="text-center">
                <a href="{{ route('dashboard.index') }}"
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Go to Dashboard
                </a>
            </div>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
