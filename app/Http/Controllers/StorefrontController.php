<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Plan;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->with(['plans' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('storefront.index', compact('products'));
    }

    public function product(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['plans' => function ($q) {
            $q->where('is_active', true)->orderBy('price');
        }]);

        return view('storefront.product', compact('product'));
    }
}
