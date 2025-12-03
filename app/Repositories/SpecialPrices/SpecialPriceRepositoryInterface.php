<?php

declare(strict_types=1);

namespace App\Repositories\SpecialPrices;

use App\Models\ProductComplement;
use App\Models\ProductSparePart;

interface SpecialPriceRepositoryInterface
{
    public function addCartItem(int $ean13): void;

    public function deleteCartItem(int $ean13): void;

    public function updateSpecialPrices(bool $force): void;

    public function shouldBeOfferedSpecialPrice(ProductComplement|ProductSparePart $product): bool;
}
