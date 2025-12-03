<?php

declare(strict_types=1);

namespace App\Repositories\Database\Order\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): bool
    {
        return $order->save();
    }

    public function changeStatus(Order $order, OrderStatus $order_status): bool
    {
        $order->status = $order_status;

        return $this->save($order);
    }

    public function create(
        float $purchase_cost,
        PaymentMethod $payment_method,
        OrderStatus $status,
        ?User $user,
        Address $shipping_address,
        Address $billing_address,
        string $order_details,
    ): Order {
        return Order::create([
            'purchase_cost' => $purchase_cost,
            'payment_method' => $payment_method,
            'status' => $status,
            'user_id' => $user ? $user->id : null,
            'shipping_address_id' => $shipping_address->id,
            'billing_address_id' => $billing_address->id,
            'order_details' => $order_details,
        ]);
    }

    public function find(string $id): ?Order
    {
        return Order::find($id);
    }

    public function paymentGatewayResponse(Order $order, string $response): bool
    {
        $order->payment_gateway_response = $response;

        return $this->save($order);
    }
}
