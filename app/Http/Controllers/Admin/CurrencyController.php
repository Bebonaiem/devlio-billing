<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $currencies = Currency::orderBy('code')->paginate(20);
        $defaultCurrency = Setting::get('default_currency', 'USD');

        return view('admin.currencies.index', compact('currencies', 'defaultCurrency'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'format' => 'nullable|string|max:50',
            'enabled' => 'boolean',
        ]);

        Currency::create($data);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'format' => 'nullable|string|max:50',
            'enabled' => 'boolean',
        ]);

        $currency->update($data);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        if (Setting::get('default_currency', 'USD') === $currency->code) {
            return back()->with('error', 'Cannot delete the default currency.');
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }

    public function setDefault(Currency $currency)
    {
        Setting::set('default_currency', $currency->code);

        return back()->with('success', "{$currency->name} set as default currency.");
    }
}
