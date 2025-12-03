<?php

declare(strict_types=1);

namespace App\Repositories\Database\Product;

use App\DTO\FilterDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseProductRepositoryInterface
{
    public function getAll(): LengthAwarePaginator;

    public function featured(): LengthAwarePaginator;

    // public function filter(FilterDTO $filters): LengthAwarePaginator;
}
