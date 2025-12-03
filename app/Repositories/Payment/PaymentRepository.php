<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Database\Order\Order\OrderRepositoryInterface;
use App\Repositories\Payment\Traits\PaymentActions;
use Illuminate\Http\RedirectResponse;

abstract class PaymentRepository implements PaymentRepositoryInterface
{
    use PaymentActions;

    public function __construct(protected OrderRepositoryInterface $orderRepository) {}

    public function redirectWithFail(Order $order, ?string $response = null): RedirectResponse
    {
        $this->orderRepository->changeStatus($order, OrderStatus::PaymentFailed);

        if ($response !== null) {
            $encoded = json_encode($response);
            $this->orderRepository->paymentGatewayResponse($order, ($encoded === false) ? '' : $encoded);
        }

        return redirect()->route('payment.purchase-failed', ['order' => $order->id]);
    }
}
