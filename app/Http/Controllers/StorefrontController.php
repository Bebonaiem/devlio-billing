<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ConfigOption;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index()
    {
        $categories = Category::where('enabled', true)
            ->with(['products' => function ($q) {
                $q->where('enabled', true)->where('hidden', false)->orderBy('name');
            }])
            ->orderBy('order')
            ->get();

        $products = Product::where('enabled', true)
            ->where('hidden', false)
            ->with(['category', 'plans' => function ($q) {
                $q->with('prices');
            }])
            ->orderBy('name')
            ->get();

        return view('storefront.index', compact('categories', 'products'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('enabled', true)
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('enabled', true)
            ->where('hidden', false)
            ->with(['plans' => function ($q) {
                $q->with('prices');
            }])
            ->orderBy('name')
            ->get();

        return view('storefront.category', compact('category', 'products'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('enabled', true)
            ->firstOrFail();

        $product->load([
            'plans' => function ($q) {
                $q->with('prices')->orderBy('sort');
            },
            'configOptions' => function ($q) {
                $q->where('hidden', false)->orderBy('sort');
            },
            'category',
        ]);

        $defaultCurrency = Currency::where('enabled', true)->first();
        $currencies = Currency::where('enabled', true)->get();

        return view('storefront.product', compact('product', 'defaultCurrency', 'currencies'));
    }
}
