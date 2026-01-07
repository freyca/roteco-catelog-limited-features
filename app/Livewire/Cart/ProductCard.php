<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\DTO\OrderProductDTO;
use App\Livewire\Buttons\Traits\HasCartInteractions;
use App\Models\BaseProduct;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductCard extends Component
{
    use HasCartInteractions;

    public BaseProduct $product;

    public BaseProduct $related_product;

    public string $path;

    public int $quantity;

    public function mount(OrderProductDTO $order_product): void
    {
        $this->product = $order_product->getProduct();
        $this->quantity = $order_product->quantity();
        $this->related_product = $this->product->disassembly->product;


        $this->path = match (true) {
            default => '/producto',
        };
    }

    #[On('refresh-cart')]
    public function render(): View
    {
        return view('livewire.cart.product-card');
    }
}
