<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $invoices = Invoice::with(['user', 'items', 'currency'])
            ->latest()
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'items', 'transactions', 'currency', 'snapshot']);

        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        return view('admin.invoices.show', compact('invoice', 'totals'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['items', 'user', 'currency', 'snapshot']);
        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'totals'));

        return $pdf->download('invoice-'.$invoice->number.'.pdf');
    }

    public function markPaid(Invoice $invoice)
    {
        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        $transaction = InvoiceTransaction::create([
            'invoice_id' => $invoice->id,
            'gateway_id' => null,
            'amount' => $totals['total'],
            'fee' => 0,
            'transaction_id' => 'MANUAL-'.strtoupper(uniqid()),
            'status' => 'succeeded',
            'is_credit_transaction' => false,
        ]);

        app(InvoiceService::class)->markPaid($invoice, $transaction);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function markOverdue(Invoice $invoice)
    {
        $invoice->update(['status' => 'overdue']);

        return back()->with('success', 'Invoice marked as overdue.');
    }

    public function markCancelled(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->transactions()->delete();
        $invoice->items()->delete();
        $invoice->snapshot()->delete();
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
