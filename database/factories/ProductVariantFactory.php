<?php

namespace Database\Factories;

use Database\Traits\WithProductDiscounts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    use WithProductDiscounts;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 10, 3000);

        return [
            'ean13' => fake()->unique()->ean13(),
            'name' => fake()->unique()->name(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'price_with_discount' => $this->isProductDiscounted($price),
            'stock' => fake()->numberBetween(10, 100),
        ];
    }
}
