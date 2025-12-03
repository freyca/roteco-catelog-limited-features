<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use App\Repositories\SpecialPrices\SpecialPriceRepositoryInterface;

class SpecialPrices implements SpecialPriceRepositoryInterface
{
    public function __construct(private readonly SpecialPriceRepositoryInterface $repository) {}

    public function updateSpecialPrices(bool $force = false): void
    {
        return;

        $this->repository->updateSpecialPrices($force);
    }

    public function addCartItem(int $ean13): void
    {
        return;

        $this->repository->addCartItem($ean13);
    }

    public function deleteCartItem(int $ean13): void
    {
        return;

        $this->repository->deleteCartItem($ean13);
    }

    public function shouldBeOfferedSpecialPrice(ProductComplement|ProductSparePart $product): bool
    {
        return false;

        return $this->repository->shouldBeOfferedSpecialPrice($product);
    }
}
