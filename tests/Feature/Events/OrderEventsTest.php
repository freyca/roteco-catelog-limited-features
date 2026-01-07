<?php

use App\Enums\Role;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\User;
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
