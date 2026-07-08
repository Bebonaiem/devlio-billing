<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AffiliateCommission;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transactions = Transaction::with(['user', 'invoice'])
            ->latest()
            ->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'invoice']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function refund(Transaction $transaction)
    {
        $transaction->update(['status' => 'refunded']);

        if ($transaction->invoice) {
            $invoice = $transaction->invoice;
            if ($invoice->transactions()->where('status', 'completed')->count() === 0) {
                $invoice->update(['status' => 'pending']);
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