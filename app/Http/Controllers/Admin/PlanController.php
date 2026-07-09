<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Product;
use App\Services\PterodactylService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plans = Plan::with(['priceable', 'prices'])->orderBy('sort')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create(PterodactylService $pterodactyl)
    {
        $products = Product::where('enabled', true)->get();
        $currencies = Currency::where('enabled', true)->get();
        try {
            $nests = $pterodactyl->getNests();
        } catch (\Exception $e) {
            $nests = [];
        }

        return view('admin.plans.create', compact('products', 'currencies', 'nests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:free,one-time,recurring',
            'billing_period' => 'nullable|integer|min:1',
            'billing_unit' => 'nullable|in:day,week,month,year',
            'sort' => 'nullable|integer|min:0',
            'memory' => 'nullable|integer|min:0',
            'cpu' => 'nullable|integer|min:0',
            'disk' => 'nullable|integer|min:0',
            'swap' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'backups' => 'nullable|integer|min:0',
            'allocations' => 'nullable|integer|min:1',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'prices' => 'nullable|array',
            'prices.*.currency_code' => 'required_with:prices|exists:currencies,code',
            'prices.*.price' => 'required_with:prices|numeric|min:0',
            'prices.*.setup_fee' => 'nullable|numeric|min:0',
        ]);

        $product = Product::find($data['product_id']);

        $plan = $product->plans()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'billing_period' => $data['billing_period'] ?? null,
            'billing_unit' => $data['billing_unit'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'priceable_type' => Product::class,
            'memory' => $data['memory'] ?? null,
            'cpu' => $data['cpu'] ?? null,
            'disk' => $data['disk'] ?? null,
            'swap' => $data['swap'] ?? 0,
            'databases' => $data['databases'] ?? 0,
            'backups' => $data['backups'] ?? 0,
            'allocations' => $data['allocations'] ?? 1,
            'nest_id' => $data['nest_id'] ?? null,
            'egg_id' => $data['egg_id'] ?? null,
        ]);

        if (! empty($data['prices'])) {
            foreach ($data['prices'] as $priceData) {
                Price::create([
                    'plan_id' => $plan->id,
                    'currency_code' => $priceData['currency_code'],
                    'price' => $priceData['price'],
                    'setup_fee' => $priceData['setup_fee'] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan, PterodactylService $pterodactyl)
    {
        $products = Product::where('enabled', true)->get();
        $currencies = Currency::where('enabled', true)->get();
        try {
            $nests = $pterodactyl->getNests();
        } catch (\Exception $e) {
            $nests = [];
        }
        $plan->load('prices');

        return view('admin.plans.edit', compact('plan', 'products', 'currencies', 'nests'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:free,one-time,recurring',
            'billing_period' => 'nullable|integer|min:1',
            'billing_unit' => 'nullable|in:day,week,month,year',
            'sort' => 'nullable|integer|min:0',
            'memory' => 'nullable|integer|min:0',
            'cpu' => 'nullable|integer|min:0',
            'disk' => 'nullable|integer|min:0',
            'swap' => 'nullable|integer|min:0',
            'databases' => 'nullable|integer|min:0',
            'backups' => 'nullable|integer|min:0',
            'allocations' => 'nullable|integer|min:1',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'prices' => 'nullable|array',
            'prices.*.id' => 'nullable|integer',
            'prices.*.currency_code' => 'required_with:prices|exists:currencies,code',
            'prices.*.price' => 'required_with:prices|numeric|min:0',
            'prices.*.setup_fee' => 'nullable|numeric|min:0',
        ]);

        $plan->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'billing_period' => $data['billing_period'] ?? null,
            'billing_unit' => $data['billing_unit'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'priceable_id' => $data['product_id'],
            'priceable_type' => Product::class,
            'memory' => $data['memory'] ?? null,
            'cpu' => $data['cpu'] ?? null,
            'disk' => $data['disk'] ?? null,
            'swap' => $data['swap'] ?? 0,
            'databases' => $data['databases'] ?? 0,
            'backups' => $data['backups'] ?? 0,
            'allocations' => $data['allocations'] ?? 1,
            'nest_id' => $data['nest_id'] ?? null,
            'egg_id' => $data['egg_id'] ?? null,
        ]);

        if (! empty($data['prices'])) {
            $existingPriceIds = collect($data['prices'])->pluck('id')->filter()->toArray();
            $plan->prices()->whereNotIn('id', $existingPriceIds)->delete();

            foreach ($data['prices'] as $priceData) {
                if (! empty($priceData['id'])) {
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

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $plan->prices()->delete();
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}
