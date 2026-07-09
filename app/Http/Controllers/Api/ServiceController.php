<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $services = Service::with(['product', 'plan', 'currency'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($services);
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        if ($service->user_id !== $request->user()->id) {
            abort(403);
        }

        $service->load(['product', 'plan', 'configs.configOption', 'configs.configValue', 'currency']);

        return response()->json($service);
    }

    public function cancel(Request $request, Service $service): JsonResponse
    {
        if ($service->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $service->isCancellable()) {
            return response()->json(['message' => 'Service cannot be cancelled'], 400);
        }

        $service->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Service cancelled']);
    }

    public function upgrade(Request $request, Service $service): JsonResponse
    {
        if ($service->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $service->isUpgradable()) {
            return response()->json(['message' => 'Service cannot be upgraded'], 400);
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // TODO: Implement upgrade logic

        return response()->json(['message' => 'Upgrade initiated']);
    }
}
