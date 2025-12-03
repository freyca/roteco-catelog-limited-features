<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\ProductFeatureValue;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // First product needs a category
        Category::factory(1)->create();

        ProductFeature::factory(1)
            ->has(ProductFeatureValue::factory())->create();

        Product::factory(10)
            ->has(Category::factory(1))
            ->hasAttached(ProductFeatureValue::find(rand(1, 10)))
            ->create();
    }
}
