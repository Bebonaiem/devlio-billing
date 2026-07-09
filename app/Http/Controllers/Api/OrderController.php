<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends Controller
{
    public function index()
    {
        $services = Service::where('user_id', request()->user()->id)
            ->with('product', 'plan', 'invoices')
            ->latest()
            ->get();

        return JsonResource::collection($services);
    }

    public function show(Service $order)
    {
        if ($order->user_id !== request()->user()->id) {
            abort(403);
        }

        $order->load('product', 'plan.prices', 'configs.configOption', 'configs.configValue', 'invoices.items');

        return new JsonResource($order);
    }
}
