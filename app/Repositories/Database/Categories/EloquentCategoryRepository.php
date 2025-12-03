<?php

declare(strict_types=1);

namespace App\Repositories\Database\Categories;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    use CacheKeys;

    public function getAll(): Collection
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        /**
         * @var Collection<int, Category>
         */
        return Cache::remember($cacheKey, 3600, function () {
            return Category::all();
        });
    }

    public function getProducts(Category $category): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        /**
         * @var LengthAwarePaginator<Product>
         */
        return $category->products()->paginate(8);
    }

    public function featured(): Collection
    {
        $cacheKey = $this->generateCacheKey(__FUNCTION__);

        /**
         * @var Collection<int, Category>
         */
        return Cache::remember($cacheKey, 3600, function () {
            $featured_categories = config('custom.featured-categories');

            return Category::whereIn('id', $featured_categories)->get();
        });
    }
}
