<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product\ProductComplement;

use App\DTO\FilterDTO;
use App\Models\ProductComplement;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EloquentProductComplementRepository implements ProductComplementRepositoryInterface
{
    use CacheKeys;

    public function getAll(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        // return Cache::remember($cacheKey, 3600, function () {
        return ProductComplement::paginate(16);
        // });
    }

    public function featured(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        return Cache::remember($cacheKey, 3600, function () {
            $featured_products = config('custom.featured-product-complements');

            return ProductComplement::whereIn('id', $featured_products)->paginate(15);
        });
    }

    public function filter(FilterDTO $filters): LengthAwarePaginator
    {
        $query = ProductComplement::where(function ($q) use ($filters) {
            $q->where('price', '>', $filters->getMinPrice())
                ->where('price_with_discount', null)
                ->orWhere('price_with_discount', '>', $filters->getMinPrice());
        })->where(function ($q) use ($filters) {
            $q->where('price', '<', $filters->getMaxPrice())
                ->where('price_with_discount', null)
                ->orWhere('price_with_discount', '<', $filters->getMaxPrice());
        });

        if ($filters->getfeatures() !== []) {
            $query = $query->whereHas('productFeatureValues', function ($query) use ($filters) {
                return $query->where('product_feature_value_id', $filters->getfeatures());
            });
        }

        return $query->paginate(16);
    }
}
