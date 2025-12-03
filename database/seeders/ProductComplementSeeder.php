<?php

namespace Database\Seeders;

use App\Models\ProductComplement;
use App\Models\ProductFeatureValue;
use Illuminate\Database\Seeder;

class ProductComplementSeeder extends Seeder
{
    public function run(): void
    {
        ProductComplement::factory(10)
            ->hasAttached(ProductFeatureValue::find(rand(1, 10)))
            ->create();
    }
}
