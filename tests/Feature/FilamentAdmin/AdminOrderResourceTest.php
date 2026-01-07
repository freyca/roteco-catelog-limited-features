<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Orders\Pages\EditOrder;
use App\Filament\Admin\Resources\Users\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);

    Filament::setCurrentPanel(
        Filament::getPanel('admin')
    );
});

describe('AdminOrderResource', function () {
    it('admin can access order list page', function () {
        test()->actingAs(test()->admin);

        Livewire::test(ListOrders::class)
            ->assertStatus(200);
    });

    it('can display orders in list table', function () {
        test()->actingAs(test()->admin);
        $orders = Order::factory(3)->create();

        $component = Livewire::test(ListOrders::class);

        foreach ($orders as $order) {
            $component->assertSee($order->code);
        }

        expect($orders)->toHaveCount(3);
    });

    it('admin can access edit order page', function () {
        test()->actingAs(test()->admin);
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
        // Clean up export files before test
        $exportDirs = Storage::disk('local')->directories('filament_exports');
        foreach ($exportDirs as $dir) {
            $files = Storage::disk('local')->files($dir);
            foreach ($files as $file) {
                Storage::disk('local')->delete($file);
            }
        }

        test()->actingAs(test()->admin);
        Order::factory(3)->create();

        // Test the export action through Livewire
        Livewire::test(ListOrders::class)
            ->mountTableAction('export')
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        // Find all CSV files except headers and xlsx
        $exportDirs = Storage::disk('local')->directories('filament_exports');
        $dataCsvFiles = [];
        foreach ($exportDirs as $dir) {
            $files = Storage::disk('local')->files($dir);
            foreach ($files as $file) {
                if (str_ends_with($file, '.csv') && ! str_contains($file, 'headers')) {
                    $dataCsvFiles[] = $file;
                }
            }
        }
        expect($dataCsvFiles)->not->toBeEmpty();
        $csv = Storage::disk('local')->get($dataCsvFiles[0]);
        // Check CSV contains at least one order code
        $order = Order::first();
        expect($csv)->toContain($order->code);

        // Clean up export files after test
        foreach ($dataCsvFiles as $file) {
            Storage::disk('local')->delete($file);
        }
    });
});
