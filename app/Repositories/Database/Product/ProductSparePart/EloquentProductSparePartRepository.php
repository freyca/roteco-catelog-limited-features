<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product\ProductSparePart;

use App\DTO\FilterDTO;
use App\Models\ProductSparePart;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EloquentProductSparePartRepository implements ProductSparePartRepositoryInterface
{
    use CacheKeys;

    public function getAll(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        // return Cache::remember($cacheKey, 3600, function () {
        return ProductSparePart::paginate(16);
        // });
    }

    public function featured(): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        /**
         * @var LengthAwarePaginator<ProductSparePart>
         */
        return Cache::remember($cacheKey, 3600, function () {
            $featured_products = config('custom.featured-product-spare-parts');

            return ProductSparePart::whereIn('id', $featured_products)->paginate(15);
        });
    }

    public function filter(FilterDTO $filters): LengthAwarePaginator
    {
        $query = ProductSparePart::where(function ($q) use ($filters) {
            $q->where('price', '>', $filters->getMinPrice())->where('price_with_discount', null)
                ->orWhere('price_with_discount', '>', $filters->getMinPrice());
        })->where(function ($q) use ($filters) {
            $q->where('price', '<', $filters->getMaxPrice())->where('price_with_discount', null)
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
