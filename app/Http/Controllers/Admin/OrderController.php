<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::with(['user', 'services'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'services.product', 'services.plan', 'services.server', 'services.invoices.items']);
        return view('admin.orders.show', compact('order'));
    }

    public function suspend(Order $order)
    {
        foreach ($order->services as $service) {
            $service->update(['status' => 'suspended']);
            if ($service->server) {
                $service->server->update(['status' => 'suspended']);
            }
        }

        return back()->with('success', 'Order services suspended successfully.');
    }

    public function unsuspend(Order $order)
    {
        foreach ($order->services as $service) {
            $service->update(['status' => 'active']);
            if ($service->server) {
                $service->server->update(['status' => 'active']);
            }
        }

        return back()->with('success', 'Order services unsuspended successfully.');
    }

    public function terminate(Order $order)
    {
        foreach ($order->services as $service) {
            $service->update(['status' => 'terminated']);
            if ($service->server) {
                $service->server->update(['status' => 'terminated']);
            }
        }

        return back()->with('success', 'Order services terminated successfully.');
    }

    public function destroy(Order $order)
    {
        foreach ($order->services as $service) {
            $service->server()?->delete();
            $service->delete();
        }
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
