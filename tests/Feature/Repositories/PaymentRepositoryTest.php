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
});

describe('Payment Service - Repositories and Logic', function () {
    it('can retrieve orders by payment method', function () {
        Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $bankTransferOrders = Order::where('payment_method', PaymentMethod::BankTransfer)->get();

        expect($bankTransferOrders)->toHaveCount(1);
    });

    it('can retrieve pending payment orders', function () {
        Order::factory()->create(['status' => OrderStatus::PaymentPending]);
        Order::factory()->create(['status' => OrderStatus::Paid]);

        $pendingOrders = Order::where('status', OrderStatus::PaymentPending)->get();

        expect($pendingOrders)->toHaveCount(1);
    });

    it('can retrieve paid orders', function () {
        Order::factory()->create(['status' => OrderStatus::Paid]);
        Order::factory()->create(['status' => OrderStatus::PaymentPending]);

        $paidOrders = Order::where('status', OrderStatus::Paid)->get();

        expect($paidOrders)->toHaveCount(1);
    });

    it('can retrieve orders for specific user', function () {
        $user1 = User::factory()->create(['role' => Role::Customer]);
        $user2 = User::factory()->create(['role' => Role::Customer]);

        Order::factory()->create(['user_id' => $user1->id]);
        Order::factory()->create(['user_id' => $user2->id]);

        $user1Orders = Order::where('user_id', $user1->id)->get();

        expect($user1Orders)->toHaveCount(1);
    });

    it('can retrieve orders by multiple statuses', function () {
        Order::factory()->create(['status' => OrderStatus::PaymentPending]);
        Order::factory()->create(['status' => OrderStatus::Paid]);
        Order::factory()->create(['status' => OrderStatus::Processing]);

        $statusOrders = Order::whereIn('status', [
            OrderStatus::PaymentPending,
            OrderStatus::Paid,
        ])->get();

        expect($statusOrders)->toHaveCount(2);
    });

    it('can retrieve orders by payment method bank transfer', function () {
        Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $bankOrders = Order::where('payment_method', PaymentMethod::BankTransfer)->get();

        expect($bankOrders)->toHaveCount(2);
    });

    it('can calculate total amount for orders', function () {
        $order1 = Order::factory()->create(['purchase_cost' => 100]);
        $order2 = Order::factory()->create(['purchase_cost' => 50]);

        $total = Order::sum('purchase_cost');

        expect($total)->toBeGreaterThan(100);
    });

    it('can count orders by status', function () {
        Order::factory()->count(3)->create(['status' => OrderStatus::Paid]);
        Order::factory()->create(['status' => OrderStatus::PaymentPending]);

        $paidCount = Order::where('status', OrderStatus::Paid)->count();

        expect($paidCount)->toBe(3);
    });

    it('can retrieve most recent orders first', function () {
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();

        $orders = Order::latest()->get();

        expect((string)$orders->first()->id)->toBe((string)$order2->id);
    });

    it('can paginate orders', function () {
        Order::factory()->count(5)->create();

        $orders = Order::paginate(2);

        expect($orders->count())->toBeLessThanOrEqual(2);
    });

    it('handles low cost orders', function () {
        $order = Order::factory()->create(['purchase_cost' => 1]);

        expect($order->purchase_cost)->toBeGreaterThan(0);
    });

    it('retrieves orders with correct user relationship', function () {
        $user = User::factory()->create(['role' => Role::Customer]);
        $order = Order::factory()->create(['user_id' => $user->id]);

        $retrievedOrder = Order::find($order->id);

        expect($retrievedOrder->user_id)->toBe($user->id);
    });
});
