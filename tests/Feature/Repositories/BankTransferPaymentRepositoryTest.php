<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    test()->admin = User::factory()->admin_notifiable()->create();
    test()->user = User::factory()->create(['role' => Role::Customer]);
});

describe('BankTransferPaymentRepository', function () {
    it('can retrieve bank transfer orders', function () {
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'user_id' => test()->user->id,
        ]);

        $bankTransferOrders = Order::where('payment_method', PaymentMethod::BankTransfer)->get();

        expect($bankTransferOrders)->toHaveCount(1);
    });

    it('retrieves only bank transfer payment method', function () {
        Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $bankOrders = Order::where('payment_method', PaymentMethod::BankTransfer)->get();

        expect($bankOrders)->toHaveCount(1);
        expect($bankOrders->first()->payment_method)->toBe(PaymentMethod::BankTransfer);
    });

    it('filters pending bank transfer orders', function () {
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => OrderStatus::PaymentPending,
        ]);
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => OrderStatus::Paid,
        ]);

        $pendingBankTransfers = Order::where('payment_method', PaymentMethod::BankTransfer)
            ->where('status', OrderStatus::PaymentPending)
            ->get();

        expect($pendingBankTransfers)->toHaveCount(1);
    });

    it('counts total bank transfer orders', function () {
        Order::factory()->count(3)->create(['payment_method' => PaymentMethod::BankTransfer]);
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $count = Order::where('payment_method', PaymentMethod::BankTransfer)->count();

        expect($count)->toBe(3);
    });

    it('calculates total amount for bank transfers', function () {
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'purchase_cost' => 100,
        ]);
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'purchase_cost' => 50,
        ]);

        $total = Order::where('payment_method', PaymentMethod::BankTransfer)->sum('purchase_cost');

        expect($total)->toBeGreaterThan(100);
    });

    it('retrieves bank transfer orders by user', function () {
        $user1 = User::factory()->create(['role' => Role::Customer]);
        $user2 = User::factory()->create(['role' => Role::Customer]);

        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'user_id' => $user1->id,
        ]);
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'user_id' => $user2->id,
        ]);

        $user1Orders = Order::where('payment_method', PaymentMethod::BankTransfer)
            ->where('user_id', $user1->id)
            ->get();

        expect($user1Orders)->toHaveCount(1);
    });

    it('retrieves completed bank transfer orders', function () {
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => OrderStatus::Paid,
        ]);
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => OrderStatus::PaymentPending,
        ]);

        $paidBankTransfers = Order::where('payment_method', PaymentMethod::BankTransfer)
            ->where('status', OrderStatus::Paid)
            ->get();

        expect($paidBankTransfers)->toHaveCount(1);
    });

    it('paginates bank transfer orders', function () {
        Order::factory()->count(5)->create(['payment_method' => PaymentMethod::BankTransfer]);

        $paginated = Order::where('payment_method', PaymentMethod::BankTransfer)->paginate(2);

        expect($paginated->count())->toBeLessThanOrEqual(2);
    });

    it('orders bank transfer by most recent', function () {
        $order1 = Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);
        sleep(1);
        $order2 = Order::factory()->create(['payment_method' => PaymentMethod::BankTransfer]);

        $orders = Order::where('payment_method', PaymentMethod::BankTransfer)->latest()->get();

        expect((string) $orders->first()->id)->toBe((string) $order2->id);
    });

    it('retrieves bank transfer with zero results', function () {
        Order::factory()->create(['payment_method' => PaymentMethod::Card]);

        $bankTransfers = Order::where('payment_method', PaymentMethod::BankTransfer)->get();

        expect($bankTransfers)->toHaveCount(0);
    });

    it('filters bank transfers by date range', function () {
        $today = now();
        $tomorrow = now()->addDay();

        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'created_at' => $today,
        ]);
        Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'created_at' => $tomorrow,
        ]);

        $todayOrders = Order::where('payment_method', PaymentMethod::BankTransfer)
            ->whereDate('created_at', $today->toDateString())
            ->get();

        expect($todayOrders->count())->toBeGreaterThanOrEqual(1);
    });

    it('retrieves bank transfer orders with relationships', function () {
        $user = User::factory()->create(['role' => Role::Customer]);
        $order = Order::factory()->create([
            'payment_method' => PaymentMethod::BankTransfer,
            'user_id' => $user->id,
        ]);

        $retrieved = Order::with('user')
            ->where('payment_method', PaymentMethod::BankTransfer)
            ->first();

        expect($retrieved->user_id)->toBe($user->id);
    });
});
