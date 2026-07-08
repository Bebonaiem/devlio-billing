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
use Illuminate\Support\Facades\Hash;

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
            'total_spent' => Transaction::where('user_id', $user->id)->where('status', 'completed')->sum('amount'),
        ];

        $recentInvoices = Invoice::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::where('user_id', $user->id)
            ->with('plan.product')
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $activity = collect();

        foreach ($recentOrders as $order) {
            $activity->push([
                'type' => 'order',
                'icon' => 'server',
                'title' => 'New Order: ' . $order->plan->product->name . ' - ' . $order->plan->name,
                'status' => $order->status,
                'date' => $order->created_at,
            ]);
        }

        foreach ($recentInvoices as $invoice) {
            $activity->push([
                'type' => 'invoice',
                'icon' => 'invoice',
                'title' => 'Invoice ' . $invoice->invoice_number . ' - $' . number_format($invoice->total, 2),
                'status' => $invoice->status,
                'date' => $invoice->created_at,
            ]);
        }

        foreach ($recentTickets as $ticket) {
            $activity->push([
                'type' => 'ticket',
                'icon' => 'support',
                'title' => 'Ticket: ' . $ticket->subject,
                'status' => $ticket->status,
                'date' => $ticket->created_at,
            ]);
        }

        $activity = $activity->sortByDesc('date')->take(10);

        return view('dashboard.index', compact('user', 'stats', 'recentInvoices', 'activity'));
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

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => $validated['password']]);

        return back()->with('success', 'Password updated successfully!');
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
