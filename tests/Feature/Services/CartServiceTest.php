<?php

use App\Enums\Role;
use App\Models\ProductSparePart;
use App\Models\User;
use App\Services\Cart;

beforeEach(function () {
    test()->user = User::factory()->create(['role' => Role::Customer]);
    test()->product1 = ProductSparePart::factory()->create();
    test()->product2 = ProductSparePart::factory()->create();
});

describe('Cart Service', function () {
    it('can add product to cart', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $result = $cart->add(test()->product1, 1);

        expect($result)->toBeTrue();
        expect($cart->hasProduct(test()->product1))->toBeTrue();
    });

    it('can remove product from cart', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 1);
        expect($cart->hasProduct(test()->product1))->toBeTrue();

        $cart->remove(test()->product1);

        expect($cart->hasProduct(test()->product1))->toBeFalse();
    });

    it('returns total quantity for product', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 3);

        expect($cart->getTotalQuantityForProduct(test()->product1))->toBe(3);
    });

    it('returns total cost for product', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 2);

        $totalCost = $cart->getTotalCostforProduct(test()->product1);
        expect($totalCost)->toBeGreaterThan(0);
    });

    it('can format product cost', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 1);

        $formattedCost = $cart->getTotalCostforProduct(test()->product1, true);
        expect($formattedCost)->toBeString();
        expect($formattedCost)->toContain('€');
    });

    it('can get total quantity in cart', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 2);
        $cart->add(test()->product2, 3);

        expect($cart->getTotalQuantity())->toBe(5);
    });

    it('can get total cost in cart', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 1);
        $cart->add(test()->product2, 1);

        $totalCost = $cart->getTotalCost();
        expect($totalCost)->toBeGreaterThan(0);
    });

    it('can check if cart is empty', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        expect($cart->isEmpty())->toBeTrue();

        $cart->add(test()->product1, 1);

        expect($cart->isEmpty())->toBeFalse();
    });

    it('can get all cart items', function () {
        test()->actingAs(test()->user);
        $cart = app(Cart::class);

        $cart->add(test()->product1, 1);
        $cart->add(test()->product2, 2);

        $items = $cart->getCart();

        expect($items)->toHaveCount(2);
    });
});
