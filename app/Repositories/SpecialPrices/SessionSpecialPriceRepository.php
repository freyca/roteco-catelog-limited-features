<?php

declare(strict_types=1);

namespace App\Repositories\SpecialPrices;

use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionSpecialPriceRepository implements SpecialPriceRepositoryInterface
{
    const SESSION = 'special_prices';

    private Collection $related_ean_13;

    private ProductComplement|ProductSparePart $product;

    public function __construct()
    {
        if (! Session::has(self::SESSION)) {
            Session::put(
                self::SESSION,
                collect([
                    'purchased' => collect(),
                    'in_cart' => collect(),
                ])
            );
        }

        $this->updateSpecialPrices();
    }

    public function updateSpecialPrices(bool $force = false): void
    {
        $this->savePurchasedProducts($force);
    }

    /**
     * This function is usually queried repeteadly
     * Since the funcion needs to query the database, we use a cache system in
     * the object to avoid getting the data from the db repeteadly
     */
    public function shouldBeOfferedSpecialPrice(ProductComplement|ProductSparePart $product): bool
    {
        $apply_discount_products = $this->getSpecialPrices();
        $all_item = $apply_discount_products->get('purchased')->concat($apply_discount_products->get('in_cart')); // @phpstan-ignore-line

        $this->generateProductRelatedEan13($product);

        $will_be_special_price_offered = false;

        $this->related_ean_13->each(function ($item) use ($all_item, &$will_be_special_price_offered) {
            if ($all_item->contains($item)) {
                $will_be_special_price_offered = true;
            }
        });

        return $will_be_special_price_offered;
    }

    public function addCartItem(int $ean13): void
    {
        $apply_discount_products = $this->getSpecialPrices();
        /**
         * @var Collection<int|null>
         */
        $in_cart = $apply_discount_products->get('in_cart');

        if ($in_cart->contains($ean13)) {
            return;
        }

        $in_cart->push($ean13);

        $this->updateSpecialPricesSession(
            $apply_discount_products->get('purchased'), // @phpstan-ignore-line
            $in_cart,
        );
    }

    public function deleteCartItem(int $ean13): void
    {
        $apply_discount_products = $this->getSpecialPrices();
        /**
         * @var Collection<int|null>
         */
        $in_cart = $apply_discount_products->get('in_cart');

        $item_key = $in_cart->search($ean13);

        if ($item_key === false) {
            return;
        }

        $in_cart->forget($item_key);

        $this->updateSpecialPricesSession(
            $apply_discount_products->get('purchased'), // @phpstan-ignore-line
            $in_cart
        );
    }

    /**
     * After a user has purchased an item, call function with force
     * parameter so it refreshes all purchased items and discounts per
     * purchased products can be applied
     */
    private function savePurchasedProducts(bool $force = false): void
    {
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        $apply_discount_products = $this->getSpecialPrices();
        /**
         * @var Collection<int|null>
         */
        $purchased = $apply_discount_products->get('purchased');
        /**
         * @var Collection<int|null>
         */
        $in_cart = $apply_discount_products->get('in_cart');

        /**
         * Avoid to repeat logic every time function is called by middlewares
         * unless we ask for force
         */
        if ($purchased->count() > 0 && $force === false) {
            return;
        }

        $this->cleanSpecialPrices();

        $user->orders->each(function ($order) use ($purchased) {
            $order->orderProducts->each(function ($order_product) use ($purchased) {
                $purchased->push($order_product->orderable()->first('ean13')?->ean13); // @phpstan-ignore-line
            });
        });

        $this->updateSpecialPricesSession($purchased, $in_cart);
    }

    /**
     * @return Collection<string, Collection<int>>
     */
    private function getSpecialPrices(): Collection
    {
        return Session::get(self::SESSION);
    }

    private function cleanSpecialPrices(): void
    {
        $apply_discount_products = $this->getSpecialPrices();
        /**
         * @var Collection<int|null>
         */
        $purchased = $apply_discount_products->get('purchased');
        $in_cart = collect();

        $this->updateSpecialPricesSession($purchased, $in_cart);
    }

    /**
     * @param  Collection<int|null>  $purchased
     * @param  Collection<int|null>  $in_cart
     */
    private function updateSpecialPricesSession(Collection $purchased, Collection $in_cart): void
    {
        Session::put(
            self::SESSION,
            collect([
                'purchased' => $purchased,
                'in_cart' => $in_cart,
            ])
        );
    }

    private function generateProductRelatedEan13(ProductComplement|ProductSparePart $product): void
    {
        if (isset($this->product) && $this->product === $product) {
            return;
        }

        if (! isset($this->product)) {
            $this->product = $product;
        }

        $this->related_ean_13 = $product->products()?->pluck('ean13');
    }
}
