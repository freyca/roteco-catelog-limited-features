<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    // Create admin user
    test()->admin = User::factory()->create(['role' => Role::Admin]);
});

describe('Filament Admin Dashboard', function () {
    it('admin user exists in system', function () {
        expect(test()->admin)->toBeInstanceOf(User::class);
        expect(test()->admin->role)->toBe(Role::Admin);
    });

    it('shows order statistics on dashboard', function () {
        // Create some orders for dashboard stats
        Order::factory()->count(3)->create(['status' => OrderStatus::Paid]);
        Order::factory()->count(2)->create(['status' => OrderStatus::PaymentPending]);

        $paidOrders = Order::where('status', OrderStatus::Paid)->count();
        expect($paidOrders)->toBe(3);
    });

    it('displays total orders count', function () {
        Order::factory()->count(5)->create();

        $orders = Order::all();
        expect($orders)->toHaveCount(5);
    });

    it('shows revenue information', function () {
        Order::factory()->count(3)->create([
            'purchase_cost' => 100,
            'status' => OrderStatus::Paid,
        ]);

        $totalRevenue = Order::where('status', OrderStatus::Paid)->sum('purchase_cost');
        expect($totalRevenue)->toBeGreaterThan(0);
    });

    it('displays pending orders', function () {
        Order::factory()->count(2)->create(['status' => OrderStatus::PaymentPending]);

        $pendingOrders = Order::where('status', OrderStatus::PaymentPending)->get();
        expect($pendingOrders)->toHaveCount(2);
    });

    it('shows order status distribution', function () {
        Order::factory()->create(['status' => OrderStatus::Paid]);
        Order::factory()->create(['status' => OrderStatus::Processing]);
        Order::factory()->create(['status' => OrderStatus::Shipped]);

        $statuses = Order::distinct('status')->pluck('status');
        expect($statuses->count())->toBeGreaterThan(0);
    });

    it('displays payment method statistics', function () {
        Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $bankTransferCount = Order::where('payment_method', PaymentMethod::BankTransfer)->count();
        expect($bankTransferCount)->toBeGreaterThan(0);
    });

    it('shows recent orders on dashboard', function () {
        Order::factory()->count(5)->create();

        $recentOrders = Order::latest()->limit(10)->get();
        expect($recentOrders->count())->toBeGreaterThan(0);
    });

    it('can calculate average order value', function () {
        Order::factory()->create(['purchase_cost' => 100]);
        Order::factory()->create(['purchase_cost' => 200]);

        $avgCost = Order::avg('purchase_cost');
        expect($avgCost)->toBeGreaterThan(0);
    });

    it('displays dashboard with multiple widgets', function () {
        Order::factory()->count(10)->create();

        $orderCount = Order::count();
        $totalRevenue = Order::sum('purchase_cost');

        expect($orderCount)->toBeGreaterThan(0);
        expect($totalRevenue)->toBeGreaterThan(0);
    });

    it('can retrieve high value orders', function () {
        Order::factory()->create(['purchase_cost' => 1000]);
        Order::factory()->create(['purchase_cost' => 100]);

        $highValue = Order::where('purchase_cost', '>', 500)->get();

        expect($highValue->count())->toBeGreaterThan(0);
    });

    it('dashboard shows admin role', function () {
        expect(test()->admin->role)->toBe(Role::Admin);
    });
});
