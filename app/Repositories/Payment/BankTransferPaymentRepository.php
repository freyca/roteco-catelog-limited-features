<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

class BankTransferPaymentRepository extends PaymentRepository
{
    public function payPurchase(Order $order): mixed
    {
        return response(null, 301, [
            'Location' => route('payment.purchase-complete', ['order' => $order->id]),
        ]);
    }

    /**
     * Just dummy function since bank transfer depends on user
     * */
    public function isGatewayOkWithPayment(Order $order, Request $request): bool
    {
        return true;
    }
}
