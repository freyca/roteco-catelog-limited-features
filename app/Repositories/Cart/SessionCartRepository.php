<?php

declare(strict_types=1);

namespace App\Repositories\Cart;

use App\DTO\OrderProductDTO;
use App\Models\BaseProduct;
use App\Models\ProductVariant;
use App\Services\PriceCalculator;
use App\Services\SpecialPrices;
use App\Traits\CurrencyFormatter;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Throwable;

class SessionCartRepository implements CartRepositoryInterface
{
    use CurrencyFormatter;

    private Collection $session_content;

    const SESSION = 'cart';

    public function __construct(
        private readonly SpecialPrices $special_prices,
        private readonly PriceCalculator $price_calculator,
    ) {
        if (! Session::has(self::SESSION)) {
            Session::put(self::SESSION, new Collection);
        }
    }

    /**
     * Functions for products
     */
    public function add(BaseProduct $product, int $quantity, bool $assemble, ?ProductVariant $variant): bool
    {
        $order_products = $this->addProductToOrder($product, $assemble, $quantity, $variant);

        $this->updateCart($order_products);
        $this->addProductToDiscountable($product);

        return true;
    }

    public function remove(BaseProduct $product, bool $assemble, ?ProductVariant $variant): void
    {
        $order_products = $this->removeProductFromOrder($product, $assemble, $variant);

        $this->updateCart($order_products);
        $this->removeProductFromDiscountable($product);
    }

    public function hasProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant): bool
    {
        try {
            $this->searchProductKey($product, $assemble, $variant);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function canBeIncremented(BaseProduct $product, bool $assemble, ?ProductVariant $variant): bool
    {
        return true;
        // try {
        //    $quantity = $this->searchProductKey($product, $assemble, $variant)['order_product_dto']->quantity();
        // } catch (Throwable $th) {
        //    return false;
        // }
        //
        // $quantity = $this->searchProductKey($product, $assemble, $variant)['order_product_dto']->quantity();
        //
        // return ($variant !== null) ? ($variant->stock - $quantity) > 0 : ($product->stock - $quantity) > 0;
    }

    public function isEmpty(): bool
    {
        return $this->getTotalQuantity() === 0;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION);
    }

    public function getCart(): Collection
    {
        /**
         * Kind of cache to avoid repetitive queries
         */
        if (! isset($this->session_content)) {
            $this->session_content = Session::get(self::SESSION);
        }

        return $this->session_content;
    }

    /**
     * Functions for quantities
     */
    public function getTotalQuantity(): int
    {
        $quantity = 0;

        foreach ($this->getCart() as $cart_item) {
            $quantity += $cart_item->quantity();
        }

        return $quantity;
    }

    public function getTotalQuantityForProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant): int
    {
        try {
            return $this->searchProductKey($product, $assemble, $variant)['order_product_dto']->quantity();
        } catch (Throwable $th) {
            return 0;
        }
    }

    /**
     * Functions for prices
     */
    public function getTotalCost(bool $formatted = false): float|string
    {
        $order_products = $this->getCart();

        $total = $this->price_calculator->getTotalCostForOrder($order_products);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalCostWithoutTaxes(bool $formatted = false): float|string
    {
        $order_products = $this->getCart();

        $total = $this->price_calculator->getTotalCostForOrderWithoutTaxes($order_products);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalDiscount(bool $formatted = false): float|string
    {
        $order_products = $this->getCart();

        $total = $this->price_calculator->getTotalDiscountForOrder($order_products);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalCostWithoutDiscount(bool $formatted = false): float|string
    {
        $order_products = $this->getCart();

        $total = $this->price_calculator->getTotaCostForOrderWithoutDiscount($order_products);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalCostforProduct(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string
    {
        $is_present = $this->searchProductKey($product, $assemble, $variant);

        $total = $this->price_calculator->getTotalCostForProduct($is_present['order_product_dto'], $is_present['order_product_dto']->quantity(), $assemble);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalCostforProductWithoutDiscount(BaseProduct $product, bool $assemble, ?ProductVariant $variant, bool $formatted = false): float|string
    {
        $is_present = $this->searchProductKey($product, $assemble, $variant);

        $total = $this->price_calculator->getTotalCostForProductWithoutDiscount($is_present['order_product_dto'], $is_present['order_product_dto']->quantity(), $assemble);

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    /**
     * Cart logic
     */
    private function addProductToOrder(BaseProduct $product, bool $assemble, int $quantity, ?ProductVariant $variant): Collection
    {
        $order_products = $this->getCart();

        try {
            $is_present = $this->searchProductKey($product, $assemble, $variant);

            $is_present['order_product_dto']->setQuantity($is_present['order_product_dto']->quantity() + $quantity);

            $order_products->replace([$is_present['key'] => $is_present['order_product_dto']]);
        } catch (Throwable $th) {
            $order_product = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: get_class($product),
                product_variant_id: ! is_null($variant) ? $variant->id : null,
                unit_price: $product->price_with_discount ? $product->price_with_discount : $product->price,
                assembly_price: ($assemble && isset($product->assembly_price)) ? $product->assembly_price : 0,
                quantity: $quantity,
                product: ! is_null($variant) ? $variant : $product,
            );

            $order_products->add($order_product);
        }

        return $order_products;
    }

    private function removeProductFromOrder(BaseProduct $product, bool $assemble, ?ProductVariant $variant): Collection
    {
        $order_products = $this->getCart();
        $key = $this->searchProductKey($product, $assemble, $variant)['key'];

        $order_products->forget($key);

        return $order_products;
    }

    /**
     * @return array{key: int, order_product_dto: OrderProductDTO}
     */
    private function searchProductKey(BaseProduct $product, bool $assemble, ?ProductVariant $variant): array
    {
        $order_products = $this->getCart();

        $product_variant_id = null;
        if (! is_null($variant)) {
            $product_variant_id = $variant->id;
        }

        $match = $order_products->filter(function (OrderProductDTO $item) use ($product, $product_variant_id, $assemble) {
            $class = get_class($product);
            $id = $product->id;
            $assembly_price = ! $assemble || ! isset($product->assembly_price) ? floatval(0) : $product->assembly_price;

            return $item->orderableType() === $class
                && $item->orderableId() === $id
                && $item->productVariantId() === $product_variant_id
                && $item->assemblyPrice() === $assembly_price;
        });

        if ($match->count() !== 1) {
            throw new Exception('Found '.$match->count().' matches of product in cart');
        }

        $key = $match->keys()->first();

        if (is_null($key)) {
            throw new Exception('This product is not in cart');
        }

        /**
         * @var OrderProductDTO
         */
        $order_product_dto = $order_products->get($key);

        return [
            'key' => intval($key),
            'order_product_dto' => $order_product_dto,
        ];
    }

    /**
     * @param  Collection<int, OrderProductDTO>  $order_products
     */
    private function updateCart(Collection $order_products): void
    {
        // Update cached session
        $this->session_content = $order_products;

        Session::put(self::SESSION, $order_products);
    }

    /**
     * Only Products has discounts in Complements and Spare Parts
     * If Product is a Variant, since complements and SpareParts are associated
     * to parent, we use parent id
     */
    private function addProductToDiscountable(BaseProduct $product): void
    {
        $this->special_prices->addCartItem($product->ean13);
    }

    private function removeProductFromDiscountable(BaseProduct $product): void
    {
        $this->special_prices->deleteCartItem($product->ean13);
    }
}
