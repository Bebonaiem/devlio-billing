<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerController extends Controller
{
    public function index()
    {
        $services = Service::where('user_id', request()->user()->id)
            ->with('product', 'plan')
            ->latest()
            ->get();

        return JsonResource::collection($services);
    }

    public function show(Service $service)
    {
        if ($service->user_id !== request()->user()->id) {
            abort(403);
        }

        $service->load('product', 'plan.prices', 'configs.configOption', 'configs.configValue');

        return new JsonResource($service);
    }
}
