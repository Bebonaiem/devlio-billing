<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', request()->user()->id)
            ->with('plan.product', 'server', 'invoices')
            ->latest()
            ->get();

        return JsonResource::collection($orders);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== request()->user()->id) {
            abort(403);
        }
        $order->load('plan.product', 'server', 'invoices.items');
        return new JsonResource($order);
    }
}
