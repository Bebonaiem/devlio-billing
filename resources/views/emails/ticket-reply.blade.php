<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Reply</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">New Reply on Ticket #{{ $ticket->id }}</h1>
                <p class="text-gray-500 mt-1">{{ $ticket->subject }}</p>
            </div>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">Hi {{ $user->first_name ?? $user->name }},</p>
                <p class="text-gray-800">You have received a new reply on your support ticket:</p>
            </div>

            <div class="bg-gray-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                <p class="text-gray-800 whitespace-pre-wrap">{{ $message }}</p>
            </div>

            <div class="text-center">
                <a href="{{ route('dashboard.tickets.show', $ticket->id) }}"
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    View Ticket
                </a>
            </div>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
