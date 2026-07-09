<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['services.product', 'invoices', 'currency'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load(['services.product', 'services.plan', 'invoices', 'currency']);

        return response()->json($order);
    }
}
