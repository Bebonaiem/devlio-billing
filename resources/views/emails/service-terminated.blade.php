<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Terminated</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Service Terminated</h1>
            </div>

            <div class="text-center mb-8">
                <p class="text-gray-600">Hi {{ $user->first_name ?? $user->name }},</p>
                <p class="text-gray-800 text-lg mt-2">Your <strong>{{ $productName }}</strong> service has been terminated.</p>
                <p class="text-gray-600 mt-2">All data associated with this service has been permanently deleted and cannot be recovered.</p>
            </div>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>If you believe this was a mistake, please contact support.</p>
                <p class="mt-1">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
