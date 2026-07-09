<?php
namespace App\Livewire;

use App\Helpers\Cart;
use App\Models\Currency;
use App\Models\Extension;
use App\Services\CheckoutService;
use App\Services\CouponService;
use Livewire\Component;

class Checkout extends Component
{
    public string $currencyCode = '';

    public string $couponCode = '';

    public ?int $selectedGatewayId = null;

    public array $addresses = [];

    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount()
    {
        $this->currencyCode = session('currency', config('settings.default_currency', 'USD'));
    }

    public function setCurrency(string $code)
    {
        $this->currencyCode = $code;
        session(['currency' => $code]);
        $this->dispatch('currencyChanged');
    }

    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            return;
        }

        try {
            $couponService = app(CouponService::class);
            $couponService->validate($this->couponCode, auth()->user());
            session(['coupon' => $this->couponCode]);
            session()->flash('success', 'Coupon applied!');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        $this->couponCode = '';
    }

    public function process()
    {
        $cart = Cart::get();

        if (! $cart || $cart->items->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');

            return;
        }

        $checkoutService = app(CheckoutService::class);

        try {
            $order = $checkoutService->processCart($cart, auth()->user());

            return redirect()->route('checkout.success', ['order' => $order->id]);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $cart = Cart::get();
        $currencies = Currency::where('enabled', true)->get();
        $gatewayExtension = Extension::where('type', 'gateway')->where('enabled', true)->first();

        return view('livewire.checkout', compact('cart', 'currencies', 'gatewayExtension'));
    }
}
