<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product\ProductSparePart;

use App\DTO\FilterDTO;
use App\Models\ProductSparePart;
use App\Repositories\Database\Product\BaseProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductSparePartRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<ProductSparePart>
     */
    public function getAll(): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<ProductSparePart>
     */
    public function featured(): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<ProductSparePart>
     */
    public function filter(FilterDTO $filters): LengthAwarePaginator;
}
