<?php

declare(strict_types=1);

namespace App\Repositories\Database\Categories;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Database\Traits\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    use CacheKeys;

    public function getAll(): Collection
    {
        /**
         * @var Collection<int, Category>
         */
        return Category::where('published', true)->get();
    }

    public function getProducts(Category $category): LengthAwarePaginator
    {
        /**
         * @var LengthAwarePaginator<Product>
         */
        return $category->products()->paginate(8);
    }

    public function featured(): Collection
    {
        /**
         * @var Collection<int, Category>
         */
        $featured_categories = config('custom.featured-categories');

        return Category::whereIn('id', $featured_categories)->get();
    }
}
