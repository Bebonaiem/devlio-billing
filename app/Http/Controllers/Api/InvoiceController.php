<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', request()->user()->id)
            ->with('items', 'currency')
            ->latest()
            ->get();

        return JsonResource::collection($invoices);
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== request()->user()->id) {
            abort(403);
        }

        $invoice->load('items', 'transactions', 'currency', 'snapshot');

        return new JsonResource($invoice);
    }
}
