<?php

declare(strict_types=1);

namespace App\Repositories\Cart;

use App\DTO\OrderProductDTO;
use App\Models\BaseProduct;
use App\Services\PriceCalculator;
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
        private readonly PriceCalculator $price_calculator,
    ) {
        if (! Session::has(self::SESSION)) {
            Session::put(self::SESSION, new Collection);
        }
    }

    /**
     * Functions for products
     */
    public function add(BaseProduct $product, int $quantity): bool
    {
        $order_products = $this->addProductToOrder($product, $quantity);

        $this->updateCart($order_products);

        return true;
    }

    public function remove(BaseProduct $product): void
    {
        $order_products = $this->removeProductFromOrder($product);

        $this->updateCart($order_products);
    }

    public function hasProduct(BaseProduct $product): bool
    {
        try {
            $this->searchProductKey($product);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function canBeIncremented(BaseProduct $product): bool
    {
        return true;
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

    public function getTotalQuantityForProduct(BaseProduct $product): int
    {
        try {
            return $this->searchProductKey($product)['order_product_dto']->quantity();
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

        $total = $this->price_calculator->getTotalCostForOrderWithTaxes($order_products);

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

    public function getTotalCostforProduct(BaseProduct $product, bool $formatted = false): float|string
    {
        $is_present = $this->searchProductKey($product);

        $total = $this->price_calculator->getTotalCostForProduct($is_present['order_product_dto'], $is_present['order_product_dto']->quantity());

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    public function getTotalCostforProductWithoutDiscount(BaseProduct $product, bool $formatted = false): float|string
    {
        $is_present = $this->searchProductKey($product);

        $total = $this->price_calculator->getTotalCostForProductWithoutDiscount($is_present['order_product_dto'], $is_present['order_product_dto']->quantity());

        return $formatted ? $this->formatCurrency($total) : $total;
    }

    /**
     * Cart logic
     */
    private function addProductToOrder(BaseProduct $product, int $quantity): Collection
    {
        $order_products = $this->getCart();

        try {
            $is_present = $this->searchProductKey($product);

            $is_present['order_product_dto']->setQuantity($is_present['order_product_dto']->quantity() + $quantity);

            $order_products->replace([$is_present['key'] => $is_present['order_product_dto']]);
        } catch (Throwable $th) {
            $order_product = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: get_class($product),
                unit_price: $product->price_with_discount ? $product->price_with_discount : $product->price,
                quantity: $quantity,
                product: $product,
            );

            $order_products->add($order_product);
        }

        return $order_products;
    }

    private function removeProductFromOrder(BaseProduct $product): Collection
    {
        $order_products = $this->getCart();
        $key = $this->searchProductKey($product)['key'];

        $order_products->forget($key);

        return $order_products;
    }

    /**
     * @return array{key: int, order_product_dto: OrderProductDTO}
     */
    private function searchProductKey(BaseProduct $product): array
    {
        $order_products = $this->getCart();

        $match = $order_products->filter(function (OrderProductDTO $item) use ($product) {
            $class = get_class($product);
            $id = $product->id;

            return $item->orderableType() === $class
                && $item->orderableId() === $id;
        });

        if ($match->count() !== 1) {
            throw new Exception('Found ' . $match->count() . ' matches of product in cart');
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
}
