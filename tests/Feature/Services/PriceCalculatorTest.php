<?php

use App\DTO\OrderProductDTO;
use App\Models\ProductSparePart;
use App\Services\PriceCalculator;

beforeEach(function () {
    test()->calculator = new PriceCalculator;
});

describe('PriceCalculator', function () {
    describe('Product cost calculations', function () {
        it('calculates single product cost without discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => null,
            ]);

            $productDTO = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product,
            );

            $cost = test()->calculator->getTotalCostForProduct($productDTO, quantity: 2);

            expect($cost)->toBe(200.0);
        });

        it('calculates single product cost with discount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            $productDTO = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product,
            );

            $cost = test()->calculator->getTotalCostForProduct($productDTO, quantity: 2, apply_discount: true);

            expect($cost)->toBe(160.0); // 80 * 2
        });

        it('calculates product cost ignoring discount when apply_discount is false', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            $productDTO = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product,
            );

            $cost = test()->calculator->getTotalCostForProduct($productDTO, quantity: 2, apply_discount: false);

            expect($cost)->toBe(200.0); // 100 * 2, discount ignored
        });
    });

    describe('Order cost calculations', function () {
        it('returns zero for empty order', function () {
            $products = collect();
            $total = test()->calculator->getTotalCostForOrder($products);

            expect($total)->toBe(0.0);
        });

        it('calculates order total with multiple products and discounts', function () {
            $product1 = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            $product2 = ProductSparePart::factory()->create([
                'price' => 50,
                'price_with_discount' => 40,
            ]);

            $dto1 = new OrderProductDTO(
                orderable_id: $product1->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product1,
            );

            $dto2 = new OrderProductDTO(
                orderable_id: $product2->id,
                orderable_type: ProductSparePart::class,
                unit_price: 50,
                quantity: 3,
                product: $product2,
            );

            $products = collect([$dto1, $dto2]);
            $total = test()->calculator->getTotalCostForOrder($products, apply_discount: true);

            expect($total)->toBe(280.0); // (80 * 2) + (40 * 3)
        });

        it('calculates order total without applying discounts', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            $dto = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product,
            );

            $products = collect([$dto]);
            $total = test()->calculator->getTotaCostForOrderWithoutDiscount($products);

            expect($total)->toBe(200.0);
        });

        it('calculates total discount amount', function () {
            $product = ProductSparePart::factory()->create([
                'price' => 100,
                'price_with_discount' => 80,
            ]);

            $dto = new OrderProductDTO(
                orderable_id: $product->id,
                orderable_type: ProductSparePart::class,
                unit_price: 100,
                quantity: 2,
                product: $product,
            );

            $products = collect([$dto]);
            $discount = test()->calculator->getTotalDiscountForOrder($products);

            expect($discount)->toBe(40.0); // (100 * 2) - (80 * 2)
        });
    });
});
