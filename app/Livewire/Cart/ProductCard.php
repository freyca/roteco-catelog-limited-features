<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\DTO\OrderProductDTO;
use App\Livewire\Buttons\Traits\HasCartInteractions;
use App\Models\BaseProduct;
use App\Models\ProductVariant;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductCard extends Component
{
    use HasCartInteractions;

    public BaseProduct $product;

    public ?ProductVariant $variant;

    public bool $assembly_status;

    public string $path;

    public int $quantity;

    public function mount(OrderProductDTO $order_product): void
    {
        $this->product = $order_product->getProduct();
        $this->variant = $order_product->getProductVariant();
        $this->assembly_status = floatval($order_product->assemblyPrice()) !== floatval(0);
        $this->quantity = $order_product->quantity();

        $this->path = match (true) {
            get_class($this->product) === 'App\Models\ProductSparePart' => '/pieza-de-repuesto',
            get_class($this->product) === 'App\Models\ProductComplement' => '/complemento',
            default => '/producto',
        };
    }

    #[On('refresh-cart')]
    public function render(): View
    {
        return view('livewire.cart.product-card');
    }
}
