<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            $plan = Plan::with('product')->find($item['plan_id']);
            if ($plan && $plan->is_active) {
                $quantity = $item['quantity'] ?? 1;
                $subtotal = $plan->price * $quantity;
                $items[$key] = [
                    'plan' => $plan,
                    'quantity' => $quantity,
                    'config' => $item['config'] ?? [],
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Plan $plan)
    {
        if (!$plan->is_active) {
            return back()->with('error', 'This plan is no longer available.');
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:10',
            'hostname' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'game_username' => 'nullable|string|max:255',
        ]);

        $cart = session()->get('cart', []);
        $key = 'plan_' . $plan->id;

        $cart[$key] = [
            'plan_id' => $plan->id,
            'quantity' => $validated['quantity'] ?? 1,
            'config' => [
                'hostname' => $validated['hostname'] ?? '',
                'email' => $validated['email'] ?? auth()->user()->email ?? '',
                'game_username' => $validated['game_username'] ?? '',
            ],
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Added to cart successfully!');
    }

    public function update(Request $request, string $key)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return back()->with('error', 'Item not found in cart.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart[$key]['quantity'] = $validated['quantity'];
        session()->put('cart', $cart);

        return back()->with('success', 'Cart updated!');
    }

    public function remove(string $key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared!');
    }
}
