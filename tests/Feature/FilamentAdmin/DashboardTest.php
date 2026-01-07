<?php

use App\Filament\Admin\Widgets\OrderStatsOverview;
use App\Filament\Admin\Widgets\OrderStatusChart;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Models\User;

beforeEach(function () {
    test()->admin = User::factory()->admin_notifiable()->create();
});

describe('Filament Admin Dashboard', function () {
    it('admin can access dashboard page', function () {
        test()->actingAs(test()->admin);
        $response = test()->get('/admin');
        $response->assertStatus(200);
        $response->assertSeeText('Panel de Control');
    });
});

describe('Filament Admin Widgets', function () {
    it('OrderStatsOverview widget renders all stats in Spanish', function () {
        \Livewire\Livewire::actingAs(test()->admin);
        \Livewire\Livewire::test(OrderStatsOverview::class)
            ->assertSeeText('Pedidos Totales')
            ->assertSeeText('Ingresos Totales')
            ->assertSeeText('Pedidos Pendientes')
            ->assertSeeText('Pedidos Completados');
    });

    it('RevenueChart widget renders in Spanish', function () {
        \Livewire\Livewire::actingAs(test()->admin);
        \Livewire\Livewire::test(RevenueChart::class)
            ->assertSeeText('Ingresos (Últimos 7 Días)');
    });

    it('OrderStatusChart widget renders in Spanish', function () {
        \Livewire\Livewire::actingAs(test()->admin);
        \Livewire\Livewire::test(OrderStatusChart::class)
            ->assertSeeText('Pedidos por Estado')
            ->assertSeeText('Pedidos');
    });
});
