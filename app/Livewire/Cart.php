<?php
namespace App\Livewire;

use Livewire\Component;

class Cart extends Component
{
    protected $listeners = ['cartUpdated' => '$refresh'];

    public function render()
    {
        $cart = \App\Helpers\Cart::get();

        return view('livewire.cart', compact('cart'));
    }

    public function removeItem(int $itemId)
    {
        \App\Helpers\Cart::remove($itemId);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity(int $itemId, int $quantity)
    {
        \App\Helpers\Cart::updateQuantity($itemId, $quantity);
        $this->dispatch('cartUpdated');
    }

    public function clearCart()
    {
        \App\Helpers\Cart::clear();
        $this->dispatch('cartUpdated');
    }

    public function applyCoupon(string $code)
    {
        try {
            \App\Helpers\Cart::applyCoupon($code);
            $this->dispatch('cartUpdated');
            session()->flash('success', 'Coupon applied successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeCoupon()
    {
        \App\Helpers\Cart::removeCoupon();
        $this->dispatch('cartUpdated');
    }
}
