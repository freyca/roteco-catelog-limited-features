<?php

use App\Models\ProductSparePart;
use App\Repositories\Cart\SessionCartRepository;
use App\Services\Cart;
use App\Services\PriceCalculator;
use App\Traits\CurrencyFormatter;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    // Clear session before each test
    Session::flush();

    $priceCalculator = new PriceCalculator;
    $repository = new SessionCartRepository($priceCalculator);
    test()->cart = new Cart($repository);
});

describe('Cart Service', function () {
    describe('Add and remove products', function () {
        it('adds product to cart', function () {
            $product = ProductSparePart::factory()->create();

            $result = test()->cart->add($product, 2);

            expect($result)->toBeTrue();
            expect(test()->cart->hasProduct($product))->toBeTrue();
            expect(test()->cart->getTotalQuantityForProduct($product))->toBe(2);
        });

        it('removes product from cart', function () {
            $product = ProductSparePart::factory()->create();

            test()->cart->add($product, 2);
            expect(test()->cart->hasProduct($product))->toBeTrue();

            test()->cart->remove($product);
            expect(test()->cart->hasProduct($product))->toBeFalse();
        });

        it('increments product quantity when adding same product twice', function () {
            $product = ProductSparePart::factory()->create();

            test()->cart->add($product, 2);
            test()->cart->add($product, 3);

            expect(test()->cart->getTotalQuantityForProduct($product))->toBe(5);
        });
    });

    describe('Product quantity', function () {
        it('gets total quantity for product', function () {
            $product = ProductSparePart::factory()->create();

            test()->cart->add($product, 5);

            $quantity = test()->cart->getTotalQuantityForProduct($product);

            expect($quantity)->toBe(5);
        });

        it('gets total quantity in cart', function () {
            $product1 = ProductSparePart::factory()->create();
            $product2 = ProductSparePart::factory()->create();

            test()->cart->add($product1, 3);
            test()->cart->add($product2, 2);

            expect(test()->cart->getTotalQuantity())->toBe(5);
        });
    });

    describe('Product cost calculations', function () {
        it('gets total cost for product with discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCostforProduct($product);

            // Should use discounted price: 80 * 2 = 160
            expect($cost)->toBe(160.0);
        });

        it('gets total cost for product without discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCostforProductWithoutDiscount($product);

            // Should use regular price: 100 * 2 = 200
            expect($cost)->toBe(200.0);
        });

        it('gets formatted total cost for product', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCostforProduct($product, true);

            // Should be formatted as currency
            $expected = CurrencyFormatter::formatCurrency(160.0);
            expect($cost)->toBe($expected);
        });
    });

    describe('Cart totals', function () {
        it('calculates total cost of cart', function () {
            $product1 = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);
            $product2 = ProductSparePart::factory()->create([
                'price' => 50,
                'price_with_discount' => 40,
            ]);

            test()->cart->add($product1, 2);  // 80 * 2 = 160
            test()->cart->add($product2, 1);  // 40 * 1 = 40

            $cost = test()->cart->getTotalCost();

            expect($cost)->toBe(200.0 * (1 + config('custom.tax_iva'))); // (160 + 40) * 1.21
        });

        it('calculates total cost without discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCostWithoutDiscount();

            // Should use regular price: 100 * 2 = 200
            expect($cost)->toBe(200.0);
        });

        it('calculates total discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $discount = test()->cart->getTotalDiscount();

            // (100 * 2) - (80 * 2) = 200 - 160 = 40
            expect($discount)->toBe(40.0);
        });

        it('calculates total cost without taxes', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCostWithoutTaxes();

            expect($cost)->toBe(160.0);
        });

        it('gets formatted total cost', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            test()->cart->add($product, 2);

            $cost = test()->cart->getTotalCost(true);

            // Should be formatted as currency
            $expected = CurrencyFormatter::formatCurrency(160.0 * (1 + config('custom.tax_iva')));
            expect($cost)->toBe($expected);
        });
    });

    describe('Product checks', function () {
        it('checks if product is in cart', function () {
            $product = ProductSparePart::factory()->create();

            expect(test()->cart->hasProduct($product))->toBeFalse();

            test()->cart->add($product, 1);

            expect(test()->cart->hasProduct($product))->toBeTrue();
        });

        it('checks if product can be incremented', function () {
            $product = ProductSparePart::factory()->create();

            // New product can always be added/incremented
            expect(test()->cart->canBeIncremented($product))->toBeTrue();
        });
    });

    describe('Cart management', function () {
        it('gets cart collection', function () {
            $product1 = ProductSparePart::factory()->create();
            $product2 = ProductSparePart::factory()->create();

            test()->cart->add($product1, 2);
            test()->cart->add($product2, 3);

            $cartItems = test()->cart->getCart();

            expect($cartItems->count())->toBe(2);
        });

        it('checks if cart is empty', function () {
            expect(test()->cart->isEmpty())->toBeTrue();

            $product = ProductSparePart::factory()->create();
            test()->cart->add($product, 1);

            expect(test()->cart->isEmpty())->toBeFalse();
        });

        it('clears cart', function () {
            $product = ProductSparePart::factory()->create();
            test()->cart->add($product, 2);

            expect(test()->cart->isEmpty())->toBeFalse();
            expect(test()->cart->getTotalQuantity())->toBe(2);

            // Clear cart and verify session is cleared
            test()->cart->clear();
            expect(Session::has('cart'))->toBeFalse();
        });
        it('persists multiple products in cart', function () {
            $product1 = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);
            $product2 = ProductSparePart::factory()->create([
                'price' => 50,
                'price_with_discount' => 40,
            ]);

            test()->cart->add($product1, 2);
            test()->cart->add($product2, 1);

            expect(test()->cart->getTotalQuantity())->toBe(3);
            expect(test()->cart->getTotalCost())->toBe(200.0 * (1 + config('custom.tax_iva'))); // (160 + 40) * 1.21
        });
    });
});
