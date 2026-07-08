<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Server;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::with(['user', 'plan.product', 'server'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'plan.product', 'server', 'invoices.items', 'transactions']);
        return view('admin.orders.show', compact('order'));
    }

    public function suspend(Order $order)
    {
        $order->update(['status' => 'suspended']);
        if ($order->server) {
            $order->server->update(['status' => 'suspended']);
        }

        return back()->with('success', 'Order suspended successfully.');
    }

    public function unsuspend(Order $order)
    {
        $order->update(['status' => 'active']);
        if ($order->server) {
            $order->server->update(['status' => 'active']);
        }

        return back()->with('success', 'Order unsuspended successfully.');
    }

    public function terminate(Order $order)
    {
        $order->update(['status' => 'terminated']);
        if ($order->server) {
            $order->server->update(['status' => 'terminated']);
        }

        return back()->with('success', 'Order terminated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
