<?php

namespace Database\Factories;

use App\Models\ProductSparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a ProductSparePart with its required Disassembly
        $disassembly = \App\Models\Disassembly::factory()->create();
        $product = ProductSparePart::factory()->for($disassembly)->create();

        $variants = $product?->productVariants;
        $variant = null;

        if ($variants !== null && count($variants) !== 0) {
            $variant = $variants->random();
        }

        $price = match (true) {
            isset($variant) => (! is_null($variant->price_with_discount)) ? $variant->price_with_discount : $variant->price,
            ! is_null($product->price_with_discount) => $product->price_with_discount,
            default => $product->price ?? 0,
        };

        return [
            'orderable_id' => $product->id,
            'orderable_type' => ProductSparePart::class,
            'product_variant_id' => isset($variant) ? $variant->id : null,
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => $price,
            'assembly_price' => $product->assembly_price ?? 0,
        ];
    }
}
