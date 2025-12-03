<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product\Product;

use App\DTO\FilterDTO;
use App\Models\Product;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EloquentProductRepository implements ProductRepositoryInterface
{
    use CacheKeys;

    public function getAll(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        // return Cache::remember($cacheKey, 3600, function () {
        return Product::paginate(16);
        // });
    }

    public function featured(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        return Cache::remember($cacheKey, 3600, function () {
            $featured_products = config('custom.featured-products');

            return Product::whereIn('id', $featured_products)->paginate(15);
        });
    }

    // public function filter(FilterDTO $filters): LengthAwarePaginator
    // {
    //    $query = Product::where(function ($q) use ($filters) {
    //        $q->where('price', '>', $filters->getMinPrice())->where('price_with_discount', null)
    //            ->orWhere('price_with_discount', '>', $filters->getMinPrice());
    //    })->where(function ($q) use ($filters) {
    //        $q->where('price', '<', $filters->getMaxPrice())->where('price_with_discount', null)
    //            ->orWhere('price_with_discount', '<', $filters->getMaxPrice());
    //    });
    //
    //    if ($filters->getCategory() !== 0) {
    //        $query = $query->where('category_id', $filters->getCategory());
    //    }
    //
    //    if ($filters->getfeatures() !== []) {
    //        $query = $query->whereHas('productFeatureValues', function ($query) use ($filters) {
    //            return $query->where('product_feature_value_id', $filters->getfeatures());
    //        });
    //    }
    //
    //    return $query->paginate(16);
    // }
}
