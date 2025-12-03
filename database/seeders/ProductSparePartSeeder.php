<?php

namespace Database\Seeders;

use App\Models\ProductFeatureValue;
use App\Models\ProductSparePart;
use Illuminate\Database\Seeder;

class ProductSparePartSeeder extends Seeder
{
    public function run(): void
    {
        ProductSparePart::factory(10)
            ->hasAttached(ProductFeatureValue::find(rand(1, 10)))
            ->create();
    }
}
