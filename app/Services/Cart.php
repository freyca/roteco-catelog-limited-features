<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BaseProduct;
use App\Models\ProductVariant;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

final class Cart implements CartRepositoryInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $repository,
    ) {}

    public function add(BaseProduct $product, int $quantity, bool $assemble, ?ProductVariant $variant): bool
    {
        try {
            $this->repository->add($product, $quantity, $assemble, $variant);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function remove(BaseProduct $product, bool $assemble, ?ProductVariant $variant): void
    {
        $this->repository->remove($product, $assemble, $variant);
    }

    public function getTotalQuantityForProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant): int
    {
        return $this->repository->getTotalQuantityForProduct($product, $assemble, $variant);
    }

    public function getTotalCostforProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string
    {
        return $this->repository->getTotalCostforProduct($product, $assemble, $variant, $formatted);
    }

    public function getTotalCostforProductWithoutDiscount(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string
    {
        return $this->repository->getTotalCostforProductWithoutDiscount($product, $assemble, $variant, $formatted);
    }

    public function getTotalQuantity(): int
    {
        return $this->repository->getTotalQuantity();
    }

    public function getTotalCost(bool $formatted = false): float|string
    {
        return $this->repository->getTotalCost($formatted);
    }

    public function getTotalCostWithoutTaxes(bool $formatted = false): float|string
    {
        return $this->repository->getTotalCostWithoutTaxes($formatted);
    }

    public function getTotalDiscount(bool $formatted = false): float|string
    {
        return $this->repository->getTotalDiscount($formatted);
    }

    public function getTotalCostWithoutDiscount(bool $formatted = false): float|string
    {
        return $this->repository->getTotalCostWithoutDiscount($formatted);
    }

    public function hasProduct(BaseProduct $product, bool $assemble_status, ?ProductVariant $variant): bool
    {
        return $this->repository->hasProduct($product, $assemble_status, $variant);
    }

    public function getCart(): Collection
    {
        return $this->repository->getCart();
    }

    public function isEmpty(): bool
    {
        return $this->repository->isEmpty();
    }

    public function clear(): void
    {
        $this->repository->clear();
    }

    public function canBeIncremented(BaseProduct $product, bool $assemble, ?ProductVariant $variant): bool
    {
        return $this->repository->canBeIncremented($product, $assemble, $variant);
    }
}
