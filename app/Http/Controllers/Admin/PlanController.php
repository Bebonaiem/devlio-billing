<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plans = Plan::with('product')->orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cpu' => 'required|integer|min:1',
            'memory' => 'required|integer|min:1',
            'disk' => 'required|integer|min:1',
            'swap' => 'integer|min:0',
            'databases' => 'integer|min:0',
            'backups' => 'integer|min:0',
            'allocations' => 'integer|min:1',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.plans.edit', compact('plan', 'products'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cpu' => 'required|integer|min:1',
            'memory' => 'required|integer|min:1',
            'disk' => 'required|integer|min:1',
            'swap' => 'integer|min:0',
            'databases' => 'integer|min:0',
            'backups' => 'integer|min:0',
            'allocations' => 'integer|min:1',
            'nest_id' => 'nullable|integer',
            'egg_id' => 'nullable|integer',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}
