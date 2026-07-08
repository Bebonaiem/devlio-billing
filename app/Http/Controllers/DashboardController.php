<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Server;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Services\PterodactylService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $stats = [
            'active_servers' => Server::where('user_id', $user->id)->where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('user_id', $user->id)->where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('user_id', $user->id)->whereIn('status', ['open', 'awaiting_reply'])->count(),
        ];

        $recentInvoices = Invoice::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('user', 'stats', 'recentInvoices'));
    }

    public function servers()
    {
        $servers = Server::where('user_id', Auth::id())
            ->with('order.plan.product')
            ->latest()
            ->paginate(10);

        return view('dashboard.servers', compact('servers'));
    }

    public function serverDetail(Server $server)
    {
        if ($server->user_id !== Auth::id()) {
            abort(403);
        }

        return view('dashboard.server-detail', compact('server'));
    }

    public function invoices()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('items', 'transactions')
            ->latest()
            ->paginate(10);

        return view('dashboard.invoices', compact('invoices'));
    }

    public function invoiceDetail(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load('items', 'transactions', 'order.plan.product');
        return view('dashboard.invoice-detail', compact('invoice'));
    }

    public function tickets()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with('messages')
            ->latest()
            ->paginate(10);

        return view('dashboard.tickets', compact('tickets'));
    }

    public function profile()
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods;
        return view('dashboard.profile', compact('user', 'paymentMethods'));
    }

    public function affiliate()
    {
        $user = Auth::user();
        $commissions = $user->affiliateCommissions()
            ->with('referredUser')
            ->latest()
            ->paginate(10);

        $referrals = $user->referrals()->count();
        $totalEarned = $user->affiliateCommissions()
            ->where('status', 'approved')
            ->sum('amount');

        return view('dashboard.affiliate', compact('user', 'commissions', 'referrals', 'totalEarned'));
    }
}
