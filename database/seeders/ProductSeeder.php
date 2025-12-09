<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // First product needs a category
        Category::factory(1)->create();

        Product::factory(10)
            ->has(Category::factory(1))
            ->create();
    }
}
