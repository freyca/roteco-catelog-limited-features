<?php

namespace Database\Factories;

use App\Enums\ProductFeatureFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductFeatures>
 */
class ProductFeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->catchPhrase(),
            'family' => fake()->randomElement(ProductFeatureFamily::cases())->value,
            'description' => fake()->realText(1000),
        ];
    }
}
