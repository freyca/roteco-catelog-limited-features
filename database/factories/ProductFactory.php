<?php

namespace Database\Factories;

use App\Models\Category;
use Database\Traits\WithProductDiscounts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
        $mandatory_assembly = false;
        $assembly_price = 0;

        $can_be_assembled = fake()->boolean(70);

        if ($can_be_assembled === true) {
            $mandatory_assembly = fake()->boolean();
            $assembly_price = fake()->randomFloat(2, 50, 500);
        }

        $name = fake()->unique()->catchPhrase();

        return [
            'name' => $name,
            'slug' => str()->slug($name),
            'ean13' => fake()->unique()->ean13(),
            // 'price' => $price,
            // 'price_with_discount' => $this->isProductDiscounted($price),
            'published' => fake()->boolean(75),
            // 'stock' => fake()->numberBetween(10, 100),
            // 'can_be_assembled' => $can_be_assembled,
            // 'mandatory_assembly' => $mandatory_assembly,
            // 'assembly_price' => $assembly_price,
            // 'dimension_length' => fake()->randomFloat(2, 5, 100),
            // 'dimension_width' => fake()->randomFloat(2, 5, 100),
            // 'dimension_height' => fake()->randomFloat(2, 5, 100),
            // 'dimension_weight' => fake()->randomFloat(2, 5, 100),
            // 'slogan' => fake()->realText(50),
            // 'meta_description' => fake()->realText(20),
            // 'short_description' => fake()->realText(200),
            // 'description' => fake()->realText(1000),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory()->create()->id,
            'main_image' => 'product-images/sample-image.png',
        ];
    }
}
