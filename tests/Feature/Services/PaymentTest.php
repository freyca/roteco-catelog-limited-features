<?php

describe('Payment Service', function () {
    beforeEach(function () {
        \App\Models\User::factory()->create(['role' => \App\Enums\Role::Admin]);
        test()->order = \App\Models\Order::factory()->create([
            'payment_method' => \App\Enums\PaymentMethod::BankTransfer,
        ]);
        test()->service = new \App\Services\Payment(test()->order);
    });

    it('calls payPurchase and isGatewayOkWithPayment on BankTransferPaymentRepository', function () {
        $service = test()->service;
        $response = $service->payPurchase();
        expect($response)->not->toBeNull();
        expect($response->getStatusCode())->toBeIn([301, 302]);
        $location = $response->headers->get('Location');
        expect($location)->toBeString();
        $route = app('router')->getRoutes()->match(\Illuminate\Http\Request::create($location));
        $routeName = $route->getName();
        expect($routeName)->not->toBeNull();
        expect($routeName)->toBeIn(['payment.purchase-complete', 'payment.purchase-failed', 'pago-completo', 'pago-fallido']);

        $gatewayOk = $service->isGatewayOkWithPayment(request());
        expect(is_bool($gatewayOk))->toBeTrue(); // Should always be boolean
    });
});

describe('PaymentActions trait', function () {
    beforeEach(function () {
        test()->trait = new class
        {
            use \App\Repositories\Payment\Traits\PaymentActions;

            public function callConvertPriceToCents($price)
            {
                return $this->convertPriceToCents($price);
            }
        };
    });

    it('convertPriceToCents converts float price to cents', function () {
        $trait = test()->trait;
        expect($trait->callConvertPriceToCents(12.34))->toBe(1234);
        expect($trait->callConvertPriceToCents(0.99))->toBe(99);
        expect($trait->callConvertPriceToCents(100.00))->toBe(10000);
    });
});
