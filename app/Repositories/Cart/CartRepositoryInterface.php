<?php

declare(strict_types=1);

namespace App\Repositories\Cart;

use App\Models\BaseProduct;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    /**
     * Functions for products
     */
    public function add(BaseProduct $product, int $quantity, bool $assemble, ?ProductVariant $variant): bool;

    public function remove(BaseProduct $product, bool $assemble, ?ProductVariant $variant): void;

    public function hasProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant): bool;

    public function canBeIncremented(BaseProduct $product, bool $assemble, ?ProductVariant $variant): bool;

    public function isEmpty(): bool;

    public function clear(): void;

    public function getCart(): Collection;

    /**
     * Functions for quantities
     */
    public function getTotalQuantity(): int;

    public function getTotalQuantityForProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant): int;

    /**
     * Functions for prices
     */
    public function getTotalCost(bool $formatted = false): float|string;

    public function getTotalCostWithoutTaxes(bool $formatted = false): float|string;

    public function getTotalDiscount(bool $formatted = false): float|string;

    public function getTotalCostWithoutDiscount(bool $formatted = false): float|string;

    public function getTotalCostforProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string;

    public function getTotalCostforProductWithoutDiscount(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string;
}
