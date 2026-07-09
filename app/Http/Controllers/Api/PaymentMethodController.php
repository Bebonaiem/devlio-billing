<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function destroy(Request $request, $paymentMethod): JsonResponse
    {
        $method = $request->user()->paymentMethods()->findOrFail($paymentMethod);

        $method->delete();

        return response()->json(['message' => 'Payment method deleted']);
    }
}
