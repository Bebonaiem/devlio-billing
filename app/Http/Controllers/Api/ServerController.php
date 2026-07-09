¿<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $servers = Server::with(['product', 'service'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($servers);
    }

    public function show(Request $request, Server $server): JsonResponse
    {
        if ($server->user_id !== $request->user()->id) {
            abort(403);
        }

        $server->load(['product', 'service', 'service.plan']);

        return response()->json($server);
    }
}
