<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Database\Order\Order\OrderRepositoryInterface;
use App\Services\Cart;
use App\Services\Payment;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly Cart $cart,
    ) {}

    public function redirectToPayment(Order $order): mixed
    {
        $paymentService = new Payment($order);

        return $paymentService->payPurchase();
    }

    public function orderFinishedOk(Order $order): View
    {
        $this->cart->clear();

        return view('pages.purchase-complete', [
            'order' => $order,
            'seotags' => new SeoTags('noindex'),
        ]);
    }

    public function orderFinishedKo(Order $order): View
    {
        $this->cart->clear();

        $this->orderRepository->changeStatus($order, OrderStatus::PaymentFailed);

        return view('pages.purchase-complete', [
            'order' => $order,
            'seotags' => new SeoTags('noindex'),
        ]);
    }
}
