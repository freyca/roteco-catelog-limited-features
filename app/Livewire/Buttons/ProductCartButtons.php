<?php

declare(strict_types=1);

namespace App\Livewire\Buttons;

use App\Livewire\Buttons\Traits\HasCartInteractions;
use App\Models\BaseProduct;
use App\Services\Cart;
use Illuminate\View\View;
use Livewire\Component;

class ProductCartButtons extends Component
{
    use HasCartInteractions;

    public BaseProduct $product;

    public function mount(
        BaseProduct $product,
        Cart $cart,
    ): void {
        $this->product = $product;
        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product);
    }

    public function render(): View
    {
        return view('livewire.buttons.product-cart-buttons');
    }
}
