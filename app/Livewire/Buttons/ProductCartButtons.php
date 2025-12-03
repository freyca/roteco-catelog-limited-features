<?php

declare(strict_types=1);

namespace App\Livewire\Buttons;

use App\Livewire\Buttons\Traits\AssemblyStatusChanger;
use App\Livewire\Buttons\Traits\HasCartInteractions;
use App\Livewire\Buttons\Traits\ProductVariantChanger;
use App\Models\BaseProduct;
use App\Services\Cart;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ProductCartButtons extends Component
{
    use AssemblyStatusChanger;
    use HasCartInteractions;
    use ProductVariantChanger;

    public BaseProduct $product;

    public Collection $variants;

    public function mount(
        BaseProduct $product,
        Collection $variants,
        Cart $cart,
    ): void {
        $this->product = $product;
        $this->variants = $variants;

        $this->configureAssembly();
        $this->maybeSetVariant();

        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant);
    }

    public function render(): View
    {
        return view('livewire.buttons.product-cart-buttons');
    }
}
