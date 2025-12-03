<?php

declare(strict_types=1);

namespace App\Repositories\Database\Categories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection<int, Category>
     */
    public function getAll(): Collection;

    /**
     * @return LengthAwarePaginator<Product>
     */
    public function getProducts(Category $category): LengthAwarePaginator;

    /**
     * @return Collection<int, Category>
     */
    public function featured(): Collection;
}
