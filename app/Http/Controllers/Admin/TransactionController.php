<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\InvoiceTransaction;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transactions = InvoiceTransaction::with(['invoice.user', 'gateway'])
            ->latest()
            ->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(InvoiceTransaction $transaction)
    {
        $transaction->load(['invoice.user', 'gateway']);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function refund(InvoiceTransaction $transaction)
    {
        $transaction->update(['status' => 'refunded']);

        if ($transaction->invoice) {
            $completedTransactions = $transaction->invoice->transactions()
                ->where('status', 'succeeded')
                ->where('id', '!=', $transaction->id)
                ->count();

            if ($completedTransactions === 0) {
                $transaction->invoice->update(['status' => 'pending']);
            }
        }

        return back()->with('success', 'Transaction refunded successfully.');
    }

    public function commissions()
    {
        $commissions = AffiliateCommission::with(['affiliate', 'referredUser'])
            ->latest()
            ->paginate(20);

        return view('admin.transactions.commissions', compact('commissions'));
    }

    public function approveCommission(AffiliateCommission $commission)
    {
        $commission->update(['status' => 'approved']);

        return back()->with('success', 'Commission approved.');
    }

    public function payCommission(AffiliateCommission $commission)
    {
        $commission->update(['status' => 'paid']);

        return back()->with('success', 'Commission marked as paid.');
    }
}
