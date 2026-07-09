<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
    ) {
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
            $this->serviceService->suspendService($service);
        }

        return back()->with('success', 'Order services suspended successfully.');
    }

    public function unsuspend(Order $order)
    {
        foreach ($order->services as $service) {
            $this->serviceService->unsuspendService($service);
        }

        return back()->with('success', 'Order services unsuspended successfully.');
    }

    public function terminate(Order $order)
    {
        foreach ($order->services as $service) {
            $this->serviceService->terminateService($service);
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
