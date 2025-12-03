<?php

use App\Filament\User\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Create an admin user for notifications
    User::factory()->create(['role' => \App\Enums\Role::Admin]);
    test()->user = User::factory()->create();
    test()->otherUser = User::factory()->create();
});

it('does not show edit order button', function () {
    $user = test()->user;
    test()->actingAs($user);
    $order = Order::factory()->for($user)->create();

    // Check in the order list page
    $component = Livewire::test(ListOrders::class)
        ->assertSuccessful();
    $component->assertDontSee('Edit');

    // Check in the order view page
    $component = Livewire::test(\App\Filament\User\Resources\Orders\Pages\ViewOrder::class, ['record' => $order->id])
        ->assertSuccessful();
    $component->assertDontSee('Edit');
});

it('shows only own orders', function () {
    $user = test()->user;
    $otherUser = test()->otherUser;
    test()->actingAs($user);
    $myOrders = Order::factory()->count(2)->for($user)->create();
    $otherOrders = Order::factory()->count(2)->for($otherUser)->create();

    $component = Livewire::test(ListOrders::class)
        ->assertSuccessful();

    foreach ($myOrders as $order) {
        $component->assertSee((string) $order->id);
    }
    foreach ($otherOrders as $order) {
        $component->assertDontSee((string) $order->id);
    }
});

it('cannot view other users order', function () {
    $user = test()->user;
    $otherUser = test()->otherUser;
    test()->actingAs($user);
    $otherOrder = Order::factory()->for($otherUser)->create();

    expect(fn () => Livewire::test(\App\Filament\User\Resources\Orders\Pages\ViewOrder::class, ['record' => $otherOrder->id]))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect(fn () => test()->get(route('filament.user.resources.orders.view', ['record' => $otherOrder->id])))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
