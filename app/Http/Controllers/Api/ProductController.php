<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('enabled', true)
            ->where('hidden', false)
            ->with('plans')
            ->orderBy('name')
            ->get();

        return JsonResource::collection($products);
    }

    public function show(Product $product)
    {
        if (!$product->enabled || $product->hidden) {
            abort(404);
        }

        $product->load(['plans.prices', 'configOptions', 'category']);

        return new JsonResource($product);
    }
}
