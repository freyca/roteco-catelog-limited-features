<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product\Product;

use App\Models\Product;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EloquentProductRepository implements ProductRepositoryInterface
{
    use CacheKeys;

    public function getAll(): LengthAwarePaginator
    {
        return Product::paginate(16);
    }

    public function featured(): LengthAwarePaginator
    {
        $featured_products = config('custom.featured-products');

        return Product::whereIn('id', $featured_products)->paginate(15);
    }
}
