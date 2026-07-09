<?php

namespace App\Http\Controllers;

use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function subscribe(Request $request, WebPushService $webPushService): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.public_key' => 'required|string',
            'keys.auth_secret' => 'required|string',
        ]);

        $success = $webPushService->subscribe(
            $request->user(),
            $validated['endpoint'],
            $validated['keys']['public_key'],
            $validated['keys']['auth_secret']
        );

        return response()->json(['success' => $success]);
    }

    public function unsubscribe(Request $request, WebPushService $webPushService): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $success = $webPushService->unsubscribe($request->user(), $validated['endpoint']);

        return response()->json(['success' => $success]);
    }
}
