<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderSaved;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use Exception;

class MaybeRestoreStock
{
    public function handle(OrderSaved $event): void
    {
        $order = $event->order;

        if ($order->wasChanged('status') && $order->status === OrderStatus::Cancelled) {
            $this->restoreStock($order);
        }
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->orderProducts as $orderItem) {
            if (! is_null($orderItem->product_variant_id)) {
                $this->restoreProductVariantQuantity($orderItem);

                continue;
            }

            $class_name = $orderItem->orderable_type;
            $product = $class_name::find($orderItem->orderable_id);

            if (is_null($product)) {
                throw new Exception('NULL product');
            }

            $product->stock = $product->stock + $orderItem->quantity;
            $product->save();
        }
    }

    private function restoreProductVariantQuantity(OrderProduct $orderItem): void
    {
        $product = ProductVariant::find($orderItem->product_variant_id);

        if (is_null($product)) {
            throw new Exception('NULL product');
        }

        $product->stock = $product->stock + $orderItem->quantity;
        $product->save();
    }
}
