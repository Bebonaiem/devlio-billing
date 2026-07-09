¿<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::with(['items', 'currency'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        $invoice->load(['items', 'transactions', 'currency', 'snapshot']);

        return response()->json($invoice);
    }

    public function pay(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($invoice->isPaid()) {
            return response()->json(['message' => 'Invoice is already paid'], 400);
        }

        // Redirect to payment gateway or return payment URL
        return response()->json([
            'payment_url' => route('checkout.pay', ['invoice' => $invoice->id]),
        ]);
    }
}
