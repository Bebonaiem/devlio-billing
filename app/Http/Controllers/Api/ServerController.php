<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::where('user_id', request()->user()->id)
            ->with('order.plan.product')
            ->latest()
            ->get();

        return JsonResource::collection($servers);
    }

    public function show(Server $server)
    {
        if ($server->user_id !== request()->user()->id) {
            abort(403);
        }
        $server->load('order.plan.product');
        return new JsonResource($server);
    }
}
