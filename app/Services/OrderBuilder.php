<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Database\Order\Order\OrderRepositoryInterface;
use App\Repositories\Database\Order\Product\OrderProductRepositoryInterface;

class OrderBuilder
{
    private Order $order;

    private ?User $user;

    private PaymentMethod $payment_method;

    private Address $shipping_address;

    private Address $billing_address;

    private string $order_details;

    public function __construct(
        private readonly Cart $cart,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderProductRepositoryInterface $orderProductRepository,
    ) {}

    public function build(AddressBuilder $addressBuilder): void
    {
        $this->user = $addressBuilder->user();
        $this->payment_method = $addressBuilder->paymentMethod();
        $this->shipping_address = $addressBuilder->shippingAddress();
        $this->billing_address = $addressBuilder->billingAddress();
        $this->order_details = $addressBuilder->orderDetails();

        $this->buildOrder();
        $this->saveOrderProducts();
    }

    public function order(): Order
    {
        return $this->order;
    }

    private function buildOrder(): void
    {
        $this->order = $this->orderRepository->create(
            purchase_cost: (float) $this->cart->getTotalCost(),
            payment_method: $this->payment_method,
            status: OrderStatus::PaymentPending,
            user: $this->user ? $this->user : null,
            shipping_address: $this->shipping_address,
            billing_address: $this->billing_address,
            order_details: $this->order_details,
        );
    }

    private function saveOrderProducts(): void
    {
        $this->orderProductRepository->save(
            $this->order,
            $this->cart->getCart()
        );
    }
}
