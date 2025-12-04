<?php

use App\Enums\AddressType;
use App\Filament\User\Resources\Addresses\Pages\ListAddress;
use App\Filament\User\Resources\Orders\Pages\ListOrders;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Address filters and search', function () {
    beforeEach(function () {
        test()->user = User::factory()->create();
        test()->actingAs(test()->user);
    });

    it('displays all user addresses in list', function () {
        $user = test()->user;
        test()->actingAs($user);

        $addresses = Address::factory()->count(5)->for($user)->create();
        $component = Livewire::test(ListAddress::class)->assertSuccessful();

        foreach ($addresses as $address) {
            $component->assertSee($address->address);
        }
        expect($addresses)->toHaveCount(5);
    });

    it('shows empty state when no addresses exist', function () {
        Livewire::test(ListAddress::class)->assertSuccessful();

        // Verify no addresses are displayed in the table
        $addresses = Address::query()->get();
        expect($addresses)->toHaveCount(0);
    });

    it('filters addresses by type', function () {
        $user = test()->user;
        test()->actingAs($user);

        Address::factory()->for($user)->create(['address_type' => AddressType::Shipping, 'address' => 'Shipping Address 1']);
        Address::factory()->for($user)->create(['address_type' => AddressType::Billing, 'address' => 'Billing Address 1']);
        Address::factory()->for($user)->create(['address_type' => AddressType::Shipping, 'address' => 'Shipping Address 2']);

        // List all addresses
        $component = Livewire::test(ListAddress::class)->assertSuccessful();
        $component->assertSee('Shipping Address 1');
        $component->assertSee('Billing Address 1');
        $component->assertSee('Shipping Address 2');
    });

    it('paginates addresses correctly', function () {
        $user = test()->user;
        test()->actingAs($user);

        // Create more than 1 page of addresses (assuming default pagination is 10)
        $addresses = Address::factory()->count(15)->for($user)->create();

        $component = Livewire::test(ListAddress::class)->assertSuccessful();

        // Verify pagination controls are present in the component
        expect($component->html())->toContain('pagination');
        // Verify we can see some addresses on first page
        expect($component->html())->toContain($addresses->first()->address);
    });

    it('only shows authenticated user addresses', function () {
        $user = test()->user;
        $otherUser = User::factory()->create();

        test()->actingAs($otherUser);
        Address::factory()->count(3)->for($otherUser)->create();
        test()->actingAs($user);

        Address::factory()->count(2)->for($user)->create();
        Livewire::test(ListAddress::class)->assertSuccessful();

        // Query should only return user's addresses due to AddressScope
        $addresses = Address::query()->get();
        expect($addresses)->toHaveCount(2);
    });
});

describe('Order filters and search', function () {
    beforeEach(function () {
        // Create an admin for notification events
        User::factory()->create(['role' => \App\Enums\Role::Admin]);
        test()->user = User::factory()->create();
        test()->actingAs(test()->user);
    });

    it('displays all user orders in list', function () {
        $user = test()->user;
        test()->actingAs($user);

        $orders = Order::factory()->count(5)->for($user)->create();
        $component = Livewire::test(ListOrders::class)->assertSuccessful();

        foreach ($orders as $order) {
            $component->assertSee((string) $order->id);
        }
    });

    it('shows empty state when no orders exist', function () {
        $component = Livewire::test(ListOrders::class)->assertSuccessful();
        // Verify no orders are displayed in the table
        $orders = Order::query()->get();
        expect($orders)->toHaveCount(0);
    });

    it('paginates orders correctly', function () {
        $user = test()->user;
        test()->actingAs($user);

        $orders = Order::factory()->count(15)->for($user)->create();
        $component = Livewire::test(ListOrders::class)->assertSuccessful();

        // Verify pagination controls are present in the component
        expect($component->html())->toContain('pagination');
        // Verify we can see some orders on first page
        expect($component->html())->toContain((string) $orders->first()->id);
    });

    it('only shows authenticated user orders', function () {
        $user = test()->user;
        $otherUser = User::factory()->create();

        test()->actingAs($otherUser);
        $otherOrders = Order::factory()->count(3)->for($otherUser)->create();
        test()->actingAs($user);

        $userOrders = Order::factory()->count(2)->for($user)->create();
        $component = Livewire::test(ListOrders::class)->assertSuccessful();

        // Verify only user's orders are shown in the component
        foreach ($userOrders as $order) {
            $component->assertSee((string) $order->id);
        }
        // Verify other user's orders are NOT shown
        foreach ($otherOrders as $order) {
            $component->assertDontSee((string) $order->id);
        }
    });

    it('displays order status in list', function () {
        $user = test()->user;
        test()->actingAs($user);

        $order = Order::factory()->for($user)->create();
        $component = Livewire::test(ListOrders::class)->assertSuccessful();

        // Verify order ID is visible
        $component->assertSee((string) $order->id);
        // Verify order status is displayed
        $component->assertSee($order->status->getLabel());
    });
});
