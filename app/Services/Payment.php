<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Repositories\Payment\BankTransferPaymentRepository;
use App\Repositories\Payment\BizumPaymentRepository;
use App\Repositories\Payment\CreditCardPaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\Payment\PayPalPaymentRepository;
use Illuminate\Http\Request;

final class Payment
{
    private readonly PaymentRepositoryInterface $repository;

    public function __construct(private Order $order)
    {
        $this->repository = match ($this->order->payment_method) {
            PaymentMethod::Card => app(CreditCardPaymentRepository::class),
            PaymentMethod::Bizum => app(BizumPaymentRepository::class),
            PaymentMethod::PayPal => app(PayPalPaymentRepository::class),
            default => app(BankTransferPaymentRepository::class),
        };
    }

    public function payPurchase(): mixed
    {
        return $this->repository->payPurchase($this->order);
    }

    public function isGatewayOkWithPayment(Request $request): bool
    {
        return $this->repository->isGatewayOkWithPayment($this->order, $request);
    }
}
