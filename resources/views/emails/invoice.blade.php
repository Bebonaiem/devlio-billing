<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 m-0 p-0">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">{{ $appName }}</h1>
                <p class="text-gray-500 mt-1">Invoice</p>
            </div>

            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Invoice Number</p>
                        <p class="font-semibold text-gray-800">{{ $invoice->number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-1">Bill To</p>
                <p class="text-gray-800">{{ $user->name }}</p>
                <p class="text-gray-600 text-sm">{{ $user->email }}</p>
            </div>

            @if($invoice->due_at)
                <div class="mb-6">
                    <p class="text-sm text-gray-500">Due Date</p>
                    <p class="text-gray-800">{{ $invoice->due_at->format('M d, Y') }}</p>
                </div>
            @endif

            <table class="w-full mb-6">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                        <th class="pb-2">Description</th>
                        <th class="pb-2 text-center">Qty</th>
                        <th class="pb-2 text-right">Price</th>
                        <th class="pb-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-800">{{ $item->description }}</td>
                            <td class="py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-3 text-right text-gray-600">${{ number_format($item->price, 2) }}</td>
                            <td class="py-3 text-right text-gray-800 font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="pt-4 text-right font-semibold text-gray-800">Total</td>
                        <td class="pt-4 text-right font-bold text-lg text-gray-800">${{ $total }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-center text-sm text-gray-500 mt-8">
                <p>Thank you for your business!</p>
                <p class="mt-1">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
