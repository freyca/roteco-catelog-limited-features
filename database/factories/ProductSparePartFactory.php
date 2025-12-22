<?php

namespace Database\Factories;

use App\Models\Disassembly;
use Database\Traits\WithProductDiscounts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSparePart>
 */
class ProductSparePartFactory extends Factory
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
        $name = fake()->unique()->catchPhrase();

        return [
            'name' => $name,
            'slug' => str()->slug($name),
            'reference' => fake()->unique()->bothify('REF-########'),
            'number_in_image' => fake()->numberBetween(1, 99),
            'self_reference' => fake()->optional()->bothify('REF-####'),
            'price' => $price,
            'price_with_discount' => $this->isProductDiscounted($price),
            'published' => fake()->boolean(75),
            'disassembly_id' => Disassembly::inRandomOrder()->first()?->id ?? Disassembly::factory(),
            // 'price_when_user_owns_product' => $price * 0.8,
            // 'stock' => fake()->numberBetween(10, 100),
            // 'dimension_length' => fake()->randomFloat(2, 5, 100),
            // 'dimension_width' => fake()->randomFloat(2, 5, 100),
            // 'dimension_height' => fake()->randomFloat(2, 5, 100),
            // 'dimension_weight' => fake()->randomFloat(2, 5, 100),
            // 'slogan' => fake()->realText(50),
            // 'meta_description' => fake()->realText(20),
            // 'short_description' => fake()->realText(200),
            // 'description' => fake()->realText(1000),
            // 'main_image' => 'product-images/sample-image.png',
            // 'images' => [
            //    'product-images/sample-image.png',
            //    'product-images/sample-image.png',
            // ],
        ];
    }

    /**
     * Mark the spare part as published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'published' => true,
        ]);
    }
}
