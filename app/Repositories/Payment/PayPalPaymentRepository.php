<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Enums\OrderStatus;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class PayPalPaymentRepository extends PaymentRepository
{
    private const OUT_LINK = 'payer-action';

    private const ACTION = 'PAYER_ACTION_REQUIRED';

    private const VALIDATION_ERROR = 'VALIDATION_ERROR';

    private const ORDER_APPROVED = 'CHECKOUT.ORDER.APPROVED';

    public function payPurchase(Order $order): mixed
    {
        try {
            $provider = $this->getProvider();

            $response = $provider->createOrder(
                $this->payPalOrderStructure($order)
            );

            $response = collect($response); // @phpstan-ignore-line

            // If response does not have ID, something has failed
            if ($response->get('id') === null) {
                $encoded = json_encode($response);

                return $this->redirectWithFail($order, ($encoded !== false) ? $encoded : null);
            }

            // If paypal does not asks for payment
            if ($response->get('status') !== self::ACTION) {
                $encoded = json_encode($response);

                return $this->redirectWithFail($order, ($encoded !== false) ? $encoded : null);
            }

            // Iterate over links to get the outer one
            $links = $response->get('links');
            foreach ($links as $link) {
                $link = collect($link); // @phpstan-ignore-line

                if ($link->get('rel') === self::OUT_LINK) {
                    return redirect()->away($link->get('href'));
                }
            }

            // Reached here, something has failed, mark order as failed and redirect user
            $encoded = json_encode($response);

            return $this->redirectWithFail($order, ($encoded !== false) ? $encoded : null);
        } catch (Throwable $throwable) {
            return $this->redirectWithFail($order);
        }
    }

    public function isGatewayOkWithPayment(Order $order, Request $request): bool
    {
        $provider = $this->getProvider();

        // Get paypal headers from requests
        $headers = [
            'auth_algo' => $request->header('PAYPAL-AUTH-ALGO', null),
            'cert_url' => $request->header('PAYPAL-CERT-URL', null),
            'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID', null),
            'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG', null),
            'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME', null),
        ];

        // Get data from request body
        $paypal_response = $request->all();

        // Get paypal webhook id, get this from paypal developer site when you create webhook
        $paypal_webhook_id = config('paypal.webhook_id');

        // gather webhook data to verify it
        $verify_data = [
            'auth_algo' => $headers['auth_algo'],
            'cert_url' => $headers['cert_url'],
            'transmission_id' => $headers['transmission_id'],
            'transmission_sig' => $headers['transmission_sig'],
            'transmission_time' => $headers['transmission_time'],
            'webhook_id' => $paypal_webhook_id,
            'webhook_event' => $paypal_response,
        ];

        // Verify webhook
        $validation = $provider->verifyWebHook($verify_data);

        $error = (is_array($validation)) ? $validation['error'] : null;

        if (is_array($error) && $error['name'] === self::VALIDATION_ERROR) {
            return false;
        }

        // Verify order status
        if ($paypal_response['resource']['purchase_units'][0]['invoice_id'] === $order->id) {
            throw new Exception('Invalid order ID '.json_encode($paypal_response));
        }

        $encoded = json_encode($paypal_response);
        $this->orderRepository->paymentGatewayResponse($order, ($encoded !== false) ? $encoded : '');

        if ($paypal_response['event_type'] !== self::ORDER_APPROVED) {
            $this->orderRepository->changeStatus($order, OrderStatus::PaymentFailed);

            return false;
        }

        $this->orderRepository->changeStatus($order, OrderStatus::Paid);

        return true;
    }

    private function getProvider(): PayPal
    {
        $provider = new PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        return $provider;
    }

    private function payPalOrderStructure(Order $order): array
    {
        return [
            'intent' => 'CAPTURE',
            'payment_source' => [
                'paypal' => [
                    'experience_contexts' => [
                        'return_url' => route('payment.purchase-complete', ['order' => $order->id]),
                        'cancel_url' => route('payment.purchase-failed', ['order' => $order->id]),
                    ],
                ],
            ],
            'purchase_units' => [
                0 => [
                    'invoice_id' => $order->id,
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => $order->purchase_cost,
                    ],
                ],
            ],
        ];
    }
}
