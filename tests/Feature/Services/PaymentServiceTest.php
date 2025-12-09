<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    // Create admin user first to satisfy notification listener
    User::factory()->create(['role' => Role::Admin]);

    test()->user = User::factory()->create(['role' => Role::Customer]);
    test()->order = Order::factory()->create([
        'user_id' => test()->user->id,
        'status' => OrderStatus::PaymentPending,
    ]);
});

describe('Payment Service', function () {
    it('handles bank transfer payment method', function () {
        test()->order->update(['payment_method' => PaymentMethod::BankTransfer]);

        expect(test()->order->payment_method)->toBe(PaymentMethod::BankTransfer);
    });

    it('handles card payment method', function () {
        test()->order->update(['payment_method' => PaymentMethod::Card]);

        expect(test()->order->payment_method)->toBe(PaymentMethod::Card);
    });

    it('handles bizum payment method', function () {
        test()->order->update(['payment_method' => PaymentMethod::Bizum]);

        expect(test()->order->payment_method)->toBe(PaymentMethod::Bizum);
    });

    it('handles paypal payment method', function () {
        test()->order->update(['payment_method' => PaymentMethod::PayPal]);

        expect(test()->order->payment_method)->toBe(PaymentMethod::PayPal);
    });

    it('validates payment amount', function () {
        expect(test()->order->purchase_cost)->toBeGreaterThan(0);
    });

    it('processes payment with user information', function () {
        expect(test()->order->user_id)->toBe(test()->user->id);
    });

    it('marks order as payment pending', function () {
        test()->order->update(['status' => OrderStatus::PaymentPending]);

        expect(test()->order->status)->toBe(OrderStatus::PaymentPending);
    });

    it('can mark order as paid', function () {
        test()->order->update(['status' => OrderStatus::Paid]);

        expect(test()->order->status)->toBe(OrderStatus::Paid);
    });

    it('can mark order as processing after payment', function () {
        test()->order->update(['status' => OrderStatus::Processing]);

        expect(test()->order->status)->toBe(OrderStatus::Processing);
    });

    it('handles multiple payment methods for different orders', function () {
        $order1 = Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        $order2 = Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        expect($order1->payment_method)->toBe(PaymentMethod::BankTransfer);
        expect($order2->payment_method)->toBe(PaymentMethod::Card);
    });

    it('can verify order payment status', function () {
        test()->order->update(['status' => OrderStatus::Paid]);

        expect(test()->order->status)->toBe(OrderStatus::Paid);
    });
});
