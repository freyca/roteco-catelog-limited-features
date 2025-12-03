<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrderProductDTO;
use App\Models\Product;
use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PriceCalculator
{
    public function __construct(public SpecialPrices $special_prices) {}

    /**
     * Product calculations
     */
    public function getTotalCostForProduct(OrderProductDTO $product, int $quantity, bool $assemble = false, bool $apply_discount = true): float
    {
        if ($apply_discount === true && $this->shouldOfferSpecialPrice($product) && ! is_null($product->priceWhenUserOwnsProduct())) {
            return $product->priceWhenUserOwnsProduct() * $quantity;
        }

        if ($apply_discount) {
            $price = ! is_null($product->priceWithDiscount()) ? $product->priceWithDiscount() : $product->priceWithoutDiscount();
        } else {
            $price = $product->priceWithoutDiscount();
        }

        $assembly_cost = 0;
        if ($assemble) {
            $assembly_price = $product->assemblyPrice();

            $assembly_cost = $assembly_price * $quantity;
        }

        return ($quantity * $price) + $assembly_cost;
    }

    public function getTotalCostForProductWithoutDiscount(OrderProductDTO $product, int $quantity, bool $assemble = false): float
    {
        return $this->getTotalCostForProduct(product: $product, quantity: $quantity, assemble: $assemble, apply_discount: false);
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
    public function getTotalCostForOrder(Collection $order_products, bool $apply_discount = true): float
    {
        $total = 0;

        /* @var OrderProductDTO $order_product */
        foreach ($order_products as $order_product) {
            $assemble = floatval($order_product->assemblyPrice()) !== floatval(0);

            $total += $this->getTotalCostForProduct(
                product: $order_product,
                quantity: $order_product->quantity(),
                assemble: $assemble,
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
        return $this->getTotalCostForOrder($order_products) * (1 - config('custom.tax_iva'));
    }

    private function shouldOfferSpecialPrice(OrderProductDTO $order_product): bool
    {
        if (Auth::user() === null) {
            return false;
        }

        if ($order_product->orderableType() === Product::class) {
            return false;
        }

        /**
         * @var ProductComplement|ProductSparePart
         */
        $product = $order_product->getProduct();

        return $this->special_prices->shouldBeOfferedSpecialPrice($product);
    }
}
