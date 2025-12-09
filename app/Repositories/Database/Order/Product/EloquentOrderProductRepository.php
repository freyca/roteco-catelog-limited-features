<?php

declare(strict_types=1);

namespace App\Repositories\Database\Order\Product;

use App\DTO\OrderProductDTO;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Collection;

class EloquentOrderProductRepository implements OrderProductRepositoryInterface
{
    /**
     * @param  Collection<int, OrderProductDTO>  $order_products
     */
    public function save(Order $order, Collection $order_products): void
    {
        // Convert DTOs to OrderProduct models
        $models = $order_products->map(function (OrderProductDTO $dto) {
            return new OrderProduct([
                'order_id' => null, // Will be set by the relationship
                'orderable_id' => $dto->orderableId(),
                'orderable_type' => $dto->orderableType(),
                'quantity' => $dto->quantity(),
                'unit_price' => $dto->unitPrice(),
            ]);
        });

        $order->orderProducts()->saveMany($models);
    }
}
