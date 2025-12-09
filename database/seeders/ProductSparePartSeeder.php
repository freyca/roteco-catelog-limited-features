<?php

namespace Database\Seeders;

use App\Models\ProductSparePart;
use Illuminate\Database\Seeder;

class ProductSparePartSeeder extends Seeder
{
    public function run(): void
    {
        ProductSparePart::factory(10)->create();
    }
}
