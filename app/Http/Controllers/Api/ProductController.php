<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'plans.prices.currency'])
            ->where('enabled', true)
            ->where('hidden', false)
            ->when($request->category, fn ($q, $category) => $q->where('category_id', $category))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->paginate($request->get('per_page', 15));

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'plans.prices.currency', 'configOptions']);

        return response()->json($product);
    }
}
