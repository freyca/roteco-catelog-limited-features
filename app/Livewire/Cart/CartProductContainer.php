<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\Cart;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartProductContainer extends Component
{
    #[On('refresh-cart')]
    public function render(Cart $cart): View
    {
        return view('livewire.cart.cart-product-container', ['cart' => $cart]);
    }
}
