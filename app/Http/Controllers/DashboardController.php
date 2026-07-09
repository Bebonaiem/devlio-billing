¿<?php
namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\CreditService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));

        $stats = [
            'active_services' => Service::where('user_id', $user->id)->where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('user_id', $user->id)->where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('user_id', $user->id)->whereIn('status', ['open', 'awaiting_reply'])->count(),
            'total_spent' => InvoiceTransaction::where('is_credit_transaction', false)
                ->whereHas('invoice', fn ($q) => $q->where('user_id', $user->id)->where('status', 'paid'))
                ->sum('amount'),
        ];

        $recentServices = Service::where('user_id', $user->id)
            ->with(['product', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        $recentInvoices = Invoice::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $activity = collect();

        foreach ($recentServices as $service) {
            $activity->push([
                'type' => 'service',
                'icon' => 'server',
                'title' => ($service->label ?? ($service->product->name ?? 'Service')),
                'status' => $service->status,
                'date' => $service->created_at,
            ]);
        }

        foreach ($recentInvoices as $invoice) {
            $totals = app(InvoiceService::class)->calculateTotal($invoice);
            $activity->push([
                'type' => 'invoice',
                'icon' => 'invoice',
                'title' => 'Invoice '.$invoice->number.' - $'.number_format($totals['total'], 2),
                'status' => $invoice->status,
                'date' => $invoice->created_at,
            ]);
        }

        foreach ($recentTickets as $ticket) {
            $activity->push([
                'type' => 'ticket',
                'icon' => 'support',
                'title' => 'Ticket: '.$ticket->subject,
                'status' => $ticket->status,
                'date' => $ticket->created_at,
            ]);
        }

        $activity = $activity->sortByDesc('date')->take(10);

        return view('dashboard.index', compact('user', 'stats', 'recentServices', 'recentInvoices', 'activity'));
    }

    public function services()
    {
        $services = Service::where('user_id', Auth::id())
            ->with(['product', 'plan', 'currency'])
            ->latest()
            ->paginate(10);

        return view('dashboard.services', compact('services'));
    }

    public function serviceDetail(Service $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        $service->load(['product', 'plan.prices', 'configs.configOption', 'configs.configValue', 'currency']);

        $invoices = $service->invoices()->latest()->take(5)->get();

        return view('dashboard.service-detail', compact('service', 'invoices'));
    }

    public function invoices()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('dashboard.invoices', compact('invoices'));
    }

    public function invoiceDetail(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['items', 'transactions', 'currency', 'snapshot']);
        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        return view('dashboard.invoice-detail', compact('invoice', 'totals'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $invoice->load(['items', 'user', 'currency', 'snapshot']);
        $totals = app(InvoiceService::class)->calculateTotal($invoice);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'totals'));

        return $pdf->download('invoice-'.$invoice->number.'.pdf');
    }

    public function tickets()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with('service')
            ->latest()
            ->paginate(10);

        return view('dashboard.tickets', compact('tickets'));
    }

    public function createTicket()
    {
        $services = Service::where('user_id', Auth::id())->whereIn('status', ['active', 'suspended'])->get();

        return view('dashboard.tickets.create', compact('services'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'service_id' => $validated['service_id'] ?? null,
            'subject' => $validated['subject'],
            'status' => 'open',
            'priority' => $validated['priority'],
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return redirect()->route('dashboard.tickets')
            ->with('success', 'Ticket created successfully.');
    }

    public function depositCredits()
    {
        $user = Auth::user();
        $currencyCode = session('currency', config('billing.default_currency', 'USD'));
        $currencies = Currency::where('enabled', true)->get();
        $creditBalance = (new CreditService)->getBalance($user, $currencyCode);

        return view('dashboard.credits', compact('user', 'currencies', 'creditBalance', 'currencyCode'));
    }

    public function processDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'currency' => 'required|string|exists:currencies,code',
        ]);

        $user = Auth::user();
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

        return redirect()->route('checkout.pay', ['invoice' => $invoice->id]);
    }

    public function profile()
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods;
        $credits = Credit::where('user_id', $user->id)->get();

        return view('dashboard.profile', compact('user', 'paymentMethods', 'credits'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
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

        if (! Hash::check($validated['current_password'], $user->password)) {
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
