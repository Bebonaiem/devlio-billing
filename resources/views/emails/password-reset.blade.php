<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Password Reset</h1>
            </div>

            <div class="text-center mb-8">
                <p class="text-gray-600">Hi {{ $user->first_name ?? $user->name }},</p>
                <p class="text-gray-800 mt-2">We received a request to reset your password. Click the button below to create a new password.</p>
            </div>

            <div class="text-center mb-8">
                <a href="{{ $resetUrl }}"
                   class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                    Reset Password
                </a>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
            </div>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>This link will expire in 60 minutes.</p>
                <p class="mt-1">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
