<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Activated</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Service Activated</h1>
            </div>

            <div class="text-center mb-8">
                <p class="text-gray-600">Hi {{ $user->first_name ?? $user->name }},</p>
                <p class="text-gray-800 text-lg mt-2">Your <strong>{{ $productName }}</strong> service is now active.</p>
            </div>

            @if($service->expires_at)
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500">Expires</p>
                    <p class="text-gray-800 font-semibold">{{ $service->expires_at->format('M d, Y') }}</p>
                </div>
            @endif

            <div class="text-center">
                <a href="{{ route('dashboard.service-detail', $service->id) }}"
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    View Service
                </a>
            </div>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
