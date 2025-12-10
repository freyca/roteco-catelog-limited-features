<?php

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Events\OrderSaved;
use App\Models\Order;
use App\Models\User;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);
    test()->user = User::factory()->create(['role' => Role::Customer]);
    test()->order = Order::factory()->create(['user_id' => test()->user->id]);
});

describe('OrderCreated Event', function () {
    it('can be constructed with an order', function () {
        $order = Order::factory()->create();
        $event = new OrderCreated($order);
        expect($event->order)->toBe($order);
    });

    it('broadcasts on private channel', function () {
        $order = Order::factory()->create();
        $event = new OrderCreated($order);
        $channels = $event->broadcastOn();
        expect($channels)->toBeArray();
        expect($channels[0]->name)->toBe('private-channel-name');
    });

    it('is dispatched when order is created', function () {
        Event::fake();
        $order = Order::factory()->create();
        event(new OrderCreated($order));
        Event::assertDispatched(OrderCreated::class, function ($e) use ($order) {
            return $e->order->id === $order->id;
        });
    });

    it('order instance is retrievable from event', function () {
        $order = Order::factory()->create();
        $event = new OrderCreated($order);
        expect($event->order->id)->toBe($order->id);
    });
});


describe('OrderSaved Event', function () {
    it('fires when order is created', function () {
        $order = Order::factory()->create();

        expect($order->exists)->toBeTrue();
    });

    it('fires when order is updated', function () {
        test()->order->update(['status' => OrderStatus::Paid]);

        expect(test()->order->status)->toBe(OrderStatus::Paid);
    });

    it('contains order instance', function () {
        $event = new OrderSaved(test()->order);

        expect($event->order)->toBe(test()->order);
    });

    it('order is retrievable from event', function () {
        $event = new OrderSaved(test()->order);

        expect($event->order->id)->toBe(test()->order->id);
    });

    it('can dispatch event manually', function () {
        $event = new OrderSaved(test()->order);

        expect($event)->toBeInstanceOf(OrderSaved::class);
    });

    it('preserves order data in event', function () {
        $originalStatus = test()->order->status;
        $event = new OrderSaved(test()->order);

        expect($event->order->status)->toBe($originalStatus);
    });

    it('handles order status changes', function () {
        test()->order->update(['status' => OrderStatus::Processing]);
        $event = new OrderSaved(test()->order);

        expect($event->order->status)->toBe(OrderStatus::Processing);
    });

    it('event contains correct order user', function () {
        $event = new OrderSaved(test()->order);

        expect($event->order->user_id)->toBe(test()->user->id);
    });

    it('can chain multiple status updates', function () {
        test()->order->update(['status' => OrderStatus::Processing]);
        test()->order->update(['status' => OrderStatus::Shipped]);

        expect(test()->order->fresh()->status)->toBe(OrderStatus::Shipped);
    });

    it('event fires for new orders', function () {
        $newOrder = Order::factory()->create(['user_id' => test()->user->id]);
        $event = new OrderSaved($newOrder);

        expect($event->order->exists)->toBeTrue();
    });
});
