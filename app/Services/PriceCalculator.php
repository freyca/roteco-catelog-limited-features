<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrderProductDTO;
use Illuminate\Support\Collection;

class PriceCalculator
{
    /**
     * Product calculations
     */
    public function getTotalCostForProduct(OrderProductDTO $product, int $quantity, bool $apply_discount = true): float
    {
        if ($apply_discount) {
            $price = ! is_null($product->priceWithDiscount()) ? $product->priceWithDiscount() : $product->priceWithoutDiscount();
        } else {
            $price = $product->priceWithoutDiscount();
        }

        return $quantity * $price;
    }

    public function getTotalCostForProductWithoutDiscount(OrderProductDTO $product, int $quantity, bool $assemble = false): float
    {
        return $this->getTotalCostForProduct(product: $product, quantity: $quantity, apply_discount: false);
    }

    public function getTotalDiscountForProduct(OrderProductDTO $product, int $quantity, bool $assemble = false): float
    {
        return $this->getTotalCostForProductWithoutDiscount($product, $quantity, $assemble) - $this->getTotalCostForProduct($product, $quantity, $assemble);
    }

    /**
     * Order calculations
     */

    /**
     * @paran Collection<int, OrderProductDTO> $order_products
     */
    private function getTotalCostForOrder(Collection $order_products, bool $apply_discount = true): float
    {
        $total = 0;

        /* @var OrderProductDTO $order_product */
        foreach ($order_products as $order_product) {
            $total += $this->getTotalCostForProduct(
                product: $order_product,
                quantity: $order_product->quantity(),
                apply_discount: $apply_discount,
            );
        }

        return $total;
    }

    public function getTotaCostForOrderWithoutDiscount(Collection $order_products): float
    {
        return $this->getTotalCostForOrder($order_products, apply_discount: false);
    }

    public function getTotalDiscountForOrder(Collection $order): float
    {
        return $this->getTotaCostForOrderWithoutDiscount($order) - $this->getTotalCostForOrder($order);
    }

    public function getTotalCostForOrderWithoutTaxes(Collection $order_products): float
    {
        return $this->getTotalCostForOrder($order_products);
    }

    public function getTotalCostForOrderWithTaxes(Collection $order_products, bool $apply_discount = true): float
    {
        return $this->getTotalCostForOrder($order_products, $apply_discount) * (1 + config('custom.tax_iva'));
    }
}
