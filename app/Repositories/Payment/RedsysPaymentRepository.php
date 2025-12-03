<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Enums\OrderStatus;
use App\Models\Order;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\Environment;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\Redsys\RedsysClient;
use Creagia\Redsys\RedsysRequest;
use Creagia\Redsys\RedsysResponse;
use Creagia\Redsys\Support\RequestParameters;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

abstract class RedsysPaymentRepository extends PaymentRepository
{
    public function createRedsysRequest(Order $order, PayMethod $payMethod): string|RedirectResponse
    {
        try {
            $redsysRequest = RedsysRequest::create(
                $this->createClient(),
                new RequestParameters(
                    amountInCents: $this->convertPriceToCents($order->purchase_cost),
                    order: Str::take($order->id, 12),
                    currency: Currency::EUR,
                    transactionType: TransactionType::Autorizacion,
                    payMethods: $payMethod,
                    merchantUrl: route('payment.gateway-notification', ['order' => $order->id]),
                    urlOk: route('payment.purchase-complete', ['order' => $order->id]),
                    urlKo: route('payment.purchase-failed', ['order' => $order->id]),
                )
            );

            return $redsysRequest->getRedirectFormHtml();
        } catch (Throwable $th) {
            return $this->redirectWithFail($order);
        }
    }

    public function isGatewayOkWithPayment(Order $order, Request $request): bool
    {
        $inputs = $request->all();

        try {
            /**
             *  @see: https://github.com/creagia/laravel-redsys/blob/main/src/Controllers/RedsysNotificationController.php#L32
             */
            $redsys_response = new RedsysResponse($this->createClient());
            $redsys_response->setParametersFromResponse($inputs);
            $redsys_response->checkResponse();

            $encoded = json_encode($redsys_response->merchantParametersArray);

            $order->payment_gateway_response = ($encoded === false) ? null : $encoded;

            $this->orderRepository->changeStatus($order, OrderStatus::Paid);

            return true;
        } catch (Exception $e) {
            $response = json_encode($inputs);
            $this->redirectWithFail($order, ($response == false) ? null : $response);
            $this->orderRepository->changeStatus($order, OrderStatus::PaymentFailed);

            return false;
        }
    }

    private function createClient(): RedsysClient
    {
        return new RedsysClient(
            merchantCode: intval(config('redsys.tpv.merchantCode')),
            secretKey: config('redsys.tpv.key'),
            terminal: intval(config('redsys.tpv.terminal')),
            environment: config('redsys.environment') === 'production' ? Environment::Production : Environment::Test,
        );
    }
}
