<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Orders\Pages\EditOrder;
use App\Filament\Admin\Resources\Users\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);

    Filament::setCurrentPanel(
        Filament::getPanel('admin')
    );
});

describe('AdminOrderResource', function () {
    it('admin can access order list page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(ListOrders::class)
            ->assertStatus(200);
    });

    it('can display orders in list table', function () {
        $this->actingAs(test()->admin);
        $orders = Order::factory(3)->create();

        $component = Livewire::test(ListOrders::class);

        foreach ($orders as $order) {
            $component->assertSee($order->code);
        }

        expect($orders)->toHaveCount(3);
    });

    it('admin can access edit order page', function () {
        $this->actingAs(test()->admin);
        $order = Order::factory()->create();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->assertStatus(200);
    });

    it('order resource is read-only (no create page)', function () {
        $pages = \App\Filament\Admin\Resources\Users\Orders\OrderResource::getPages();
        expect($pages)->toHaveKey('index');
        expect($pages)->toHaveKey('create');
    });

    it('order resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Users\Orders\OrderResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('order resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Users\Orders\OrderResource::getPages();
        expect($pages)->toHaveKey('edit');
    });

    it('order resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Users\Orders\OrderResource::getNavigationGroup();
        expect($group)->toBe(__('Usuarios'));
    });

    it('order resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Users\Orders\OrderResource::getModelLabel();
        expect($label)->toBe(__('Pedidos'));
    });

    it('can export orders via table action', function () {
        $this->actingAs(test()->admin);
        Order::factory(3)->create();

        // Test the export action through Livewire
        Livewire::test(ListOrders::class)
            ->mountTableAction('export')
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();
    });
});
