<?php

use App\Models\Address;
use App\Models\Category;
use App\Models\Disassembly;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductSparePart;
use App\Models\User;

test('db has items after been seeded', function () {
    expect(Category::count())->toBe(0);
    expect(Order::count())->toBe(0);
    expect(Product::count())->toBe(0);
    expect(User::count())->toBe(0);
    expect(Address::count())->toBe(0);
    expect(Disassembly::count())->toBe(0);
    expect(ProductSparePart::count())->toBe(0);

    User::factory()->admin()->create();

    // Run the seeder
    $this->seed();

    // Verify seeding created expected data
    expect(Category::count())->toBeGreaterThan(0);
    expect(Order::count())->toBeGreaterThan(0);
    expect(Product::count())->toBeGreaterThan(0);
    expect(User::count())->toBeGreaterThan(0);
    expect(Address::count())->toBeGreaterThan(0);
    expect(Disassembly::count())->toBeGreaterThan(0);
    expect(ProductSparePart::count())->toBeGreaterThan(0);
});

test('order belongs to expected user and has product spare parts', function () {
    // Create test data
    $user = User::factory()->create();
    User::factory()->admin()->create();

    $disassembly = Disassembly::factory()->create();
    $spareParts = ProductSparePart::factory(3)->create([
        'disassembly_id' => $disassembly->id,
    ]);

    $order = Order::factory()->create(['user_id' => $user->id]);

    // Create order products with spare parts as orderable
    $spareParts->each(function ($sparePart) use ($order) {
        OrderProduct::factory()->create([
            'order_id' => $order->id,
            'orderable_id' => $sparePart->id,
            'orderable_type' => ProductSparePart::class,
        ]);
    });

    // Verify order exists
    expect(Order::find($order->id))->not->toBeNull();

    // Verify order belongs to expected user
    expect($order->user_id)->toBe($user->id);
    expect($order->user()->first()->id)->toBe($user->id);

    // Verify order has expected product spare parts
    $orderProducts = $order->orderProducts;
    expect($orderProducts->count())->toBe(3);

    // Verify each order product is a spare part
    $orderProducts->each(function ($orderProduct) use ($spareParts) {
        expect($orderProduct->orderable_type)->toBe(ProductSparePart::class);
        expect($spareParts->pluck('id')->contains($orderProduct->orderable_id))->toBeTrue();
    });
});
