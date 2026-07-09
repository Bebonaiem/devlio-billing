<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_services' => Service::where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'total_revenue' => InvoiceTransaction::where('status', 'succeeded')
                ->where('is_credit_transaction', false)
                ->where('amount', '>', 0)
                ->sum('amount'),
            'monthly_revenue' => InvoiceTransaction::where('status', 'succeeded')
                ->where('is_credit_transaction', false)
                ->where('amount', '>', 0)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'suspended_services' => Service::where('status', 'suspended')->count(),
        ];

        $recentServices = Service::with(['user', 'product', 'plan'])
            ->latest()
            ->take(10)
            ->get();

        $recentTransactions = InvoiceTransaction::with(['invoice.user', 'gateway'])
            ->where('status', 'succeeded')
            ->latest()
            ->take(10)
            ->get();

        $revenueByMonth = InvoiceTransaction::where('status', 'succeeded')
            ->where('is_credit_transaction', false)
            ->where('amount', '>', 0)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $revenueLabels = $revenueByMonth->map(function ($item) {
            return Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
        })->toArray();

        $revenueData = $revenueByMonth->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'stats', 'recentServices', 'recentTransactions', 'revenueLabels', 'revenueData'
        ));
    }

    public function users()
    {
        $users = User::withCount('services', 'invoices')->with('credits')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function userDetail(User $user)
    {
        $user->load(['services.product', 'services.plan', 'invoices', 'tickets', 'credits']);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.user-detail', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|exists:roles,name',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        if ($request->role) {
            $user->syncRoles([$request->role]);
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->tickets()->delete();
        $user->services()->delete();
        $user->invoices()->delete();
        $user->credits()->delete();
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}
