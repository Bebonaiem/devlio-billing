<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function deposit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'currency' => 'required|string|exists:currencies,code',
        ]);

        $user = $request->user();
        $amount = round((float) $validated['amount'], 2);
        $currencyCode = $validated['currency'];

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createInvoice($user, [
            [
                'quantity' => 1,
                'price' => $amount,
                'description' => "Credit Deposit ({$currencyCode} ".number_format($amount, 2).')',
            ],
        ], $currencyCode);

        return response()->json([
            'message' => 'Credit deposit invoice created',
            'invoice_id' => $invoice->id,
            'payment_url' => route('checkout.pay', ['invoice' => $invoice->id]),
        ]);
    }
}
