<?php

declare(strict_types=1);

namespace App\Repositories\Database\Order\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;

interface OrderRepositoryInterface
{
    public function save(Order $order): bool;

    public function changeStatus(Order $order, OrderStatus $order_status): bool;

    public function create(
        float $purchase_cost,
        PaymentMethod $payment_method,
        OrderStatus $status,
        ?User $user,
        Address $shipping_address,
        Address $billing_address,
        string $order_details,
    ): Order;

    public function find(string $id): ?Order;

    public function paymentGatewayResponse(Order $order, string $response): bool;
}
