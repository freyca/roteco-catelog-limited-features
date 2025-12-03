<?php

declare(strict_types=1);

namespace App\Livewire\Buttons;

use App\Services\Cart;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $cartItems = 0;

    #[On('refresh-cart')]
    public function render(): View
    {
        /** @var Cart */
        $cart = app(Cart::class);

        $this->cartItems = $cart->getTotalQuantity();

        return view('livewire.buttons.cart-icon');
    }
}
