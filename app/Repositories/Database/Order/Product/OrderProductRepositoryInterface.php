<?php

declare(strict_types=1);

namespace App\Repositories\Database\Order\Product;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Collection;

interface OrderProductRepositoryInterface
{
    /**
     * @param  Collection<int, OrderProduct>  $order_products
     */
    public function save(Order $order, Collection $order_products): void;
}
