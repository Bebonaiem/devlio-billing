<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

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
            'active_orders' => Order::where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'total_revenue' => Transaction::where('status', 'completed')
                ->where('amount', '>', 0)
                ->sum('amount'),
            'monthly_revenue' => Transaction::where('status', 'completed')
                ->where('amount', '>', 0)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'suspended_servers' => Order::where('status', 'suspended')->count(),
        ];

        $recentOrders = Order::with(['user', 'plan.product'])
            ->latest()
            ->take(10)
            ->get();

        $recentTransactions = Transaction::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentTransactions'));
    }

    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $keys = [
            'site_name', 'site_description', 'currency', 'tax_rate',
            'invoice_prefix', 'grace_days', 'terminate_days',
            'affiliate_rate', 'smtp_host', 'smtp_port',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function users()
    {
        $users = User::withCount('orders', 'invoices')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function userDetail(User $user)
    {
        $user->load(['orders.plan.product', 'invoices', 'transactions', 'tickets']);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.user-detail', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'credit_balance' => 'required|numeric|min:0',
            'role' => 'nullable|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'credit_balance' => $request->credit_balance,
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
        $user->servers()->delete();
        $user->orders()->delete();
        $user->invoices()->delete();
        $user->transactions()->delete();
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}
