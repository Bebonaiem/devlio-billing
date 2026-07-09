<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ConfigOption;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\PterodactylService;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::with(['products' => function ($q) {
            $q->withCount('plans');
        }])->orderBy('order')->get();

        $products = Product::with(['category', 'plans' => function ($q) {
            $q->with('prices');
        }])->orderBy('name')->get();

        return view('admin.products.index', compact('categories', 'products'));
    }

    public function create()
    {
        $categories = Category::where('enabled', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'per_user_limit' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'hidden' => 'boolean',
            'allow_quantity' => 'boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('enabled', true)->orderBy('name')->get();
        $product->load('plans.prices', 'configOptions');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'per_user_limit' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'hidden' => 'boolean',
            'allow_quantity' => 'boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function plans(Product $product, PterodactylService $pterodactyl)
    {
        $product->load(['plans' => function ($q) {
            $q->with('prices')->orderBy('sort');
        }, 'configOptions']);

        $currencies = Currency::where('enabled', true)->get();
        $configOptions = ConfigOption::where('parent_id', null)->with('children')->orderBy('sort')->get();

        $nests = $pterodactyl->getNests();

        return view('admin.products.plans', compact('product', 'currencies', 'configOptions', 'nests'));
    }

    public function storePlan(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:free,one-time,recurring',
            'billing_period' => 'nullable|integer|min:1',
            'billing_unit' => 'nullable|in:day,week,month,year',
            'sort' => 'nullable|integer|min:0',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'memory' => 'nullable|integer|min:0',
            'cpu' => 'nullable|integer|min:0',
            'disk' => 'nullable|integer|min:0',
            'swap' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'backups' => 'nullable|integer|min:0',
            'allocations' => 'nullable|integer|min:0',
            'prices' => 'nullable|array',
            'prices.*.currency_code' => 'required_with:prices|exists:currencies,code',
            'prices.*.price' => 'required_with:prices|numeric|min:0',
            'prices.*.setup_fee' => 'nullable|numeric|min:0',
        ]);

        $plan = $product->plans()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'billing_period' => $validated['billing_period'] ?? null,
            'billing_unit' => $validated['billing_unit'] ?? null,
            'sort' => $validated['sort'] ?? 0,
            'nest_id' => $validated['nest_id'] ?? null,
            'egg_id' => $validated['egg_id'] ?? null,
            'memory' => $validated['memory'] ?? null,
            'cpu' => $validated['cpu'] ?? null,
            'disk' => $validated['disk'] ?? null,
            'swap' => $validated['swap'] ?? 0,
            'databases' => $validated['databases'] ?? 0,
            'backups' => $validated['backups'] ?? 0,
            'allocations' => $validated['allocations'] ?? 1,
            'priceable_type' => Product::class,
        ]);

        if (!empty($validated['prices'])) {
            foreach ($validated['prices'] as $priceData) {
                Price::create([
                    'plan_id' => $plan->id,
                    'currency_code' => $priceData['currency_code'],
                    'price' => $priceData['price'],
                    'setup_fee' => $priceData['setup_fee'] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.products.plans', $product)
            ->with('success', 'Plan created successfully.');
    }

    public function updatePlan(Request $request, Product $product, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:free,one-time,recurring',
            'billing_period' => 'nullable|integer|min:1',
            'billing_unit' => 'nullable|in:day,week,month,year',
            'sort' => 'nullable|integer|min:0',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'memory' => 'nullable|integer|min:0',
            'cpu' => 'nullable|integer|min:0',
            'disk' => 'nullable|integer|min:0',
            'swap' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'backups' => 'nullable|integer|min:0',
            'allocations' => 'nullable|integer|min:0',
            'prices' => 'nullable|array',
            'prices.*.id' => 'nullable|integer',
            'prices.*.currency_code' => 'required_with:prices|exists:currencies,code',
            'prices.*.price' => 'required_with:prices|numeric|min:0',
            'prices.*.setup_fee' => 'nullable|numeric|min:0',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'billing_period' => $validated['billing_period'] ?? null,
            'billing_unit' => $validated['billing_unit'] ?? null,
            'sort' => $validated['sort'] ?? 0,
            'nest_id' => $validated['nest_id'] ?? null,
            'egg_id' => $validated['egg_id'] ?? null,
            'memory' => $validated['memory'] ?? null,
            'cpu' => $validated['cpu'] ?? null,
            'disk' => $validated['disk'] ?? null,
            'swap' => $validated['swap'] ?? 0,
            'databases' => $validated['databases'] ?? 0,
            'backups' => $validated['backups'] ?? 0,
            'allocations' => $validated['allocations'] ?? 1,
        ]);

        if (!empty($validated['prices'])) {
            $existingPriceIds = collect($validated['prices'])->pluck('id')->filter()->toArray();
            $plan->prices()->whereNotIn('id', $existingPriceIds)->delete();

            foreach ($validated['prices'] as $priceData) {
                if (!empty($priceData['id'])) {
                    Price::where('id', $priceData['id'])->update([
                        'currency_code' => $priceData['currency_code'],
                        'price' => $priceData['price'],
                        'setup_fee' => $priceData['setup_fee'] ?? 0,
                    ]);
                } else {
                    Price::create([
                        'plan_id' => $plan->id,
                        'currency_code' => $priceData['currency_code'],
                        'price' => $priceData['price'],
                        'setup_fee' => $priceData['setup_fee'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.plans', $product)
            ->with('success', 'Plan updated successfully.');
    }

    public function destroyPlan(Product $product, Plan $plan)
    {
        $plan->prices()->delete();
        $plan->configOptions()->detach();
        $plan->delete();

        return redirect()->route('admin.products.plans', $product)
            ->with('success', 'Plan deleted successfully.');
    }

    public function updateConfigOptions(Request $request, Product $product)
    {
        $validated = $request->validate([
            'config_options' => 'nullable|array',
            'config_options.*' => 'integer|exists:config_options,id',
        ]);

        $product->configOptions()->sync($validated['config_options'] ?? []);

        return back()->with('success', 'Config options updated successfully.');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->orderBy('order')->get();
        return view('admin.products.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        return back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
