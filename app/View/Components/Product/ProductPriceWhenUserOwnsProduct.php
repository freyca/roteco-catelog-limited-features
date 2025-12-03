<?php

declare(strict_types=1);

namespace App\View\Components\Product;

use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductPriceWhenUserOwnsProduct extends Component
{
    public function __construct(
        public ProductComplement|ProductSparePart $product
    ) {}

    public function render(): View
    {
        return view('components.product.product-price-when-user-owns-product');
    }
}
