<?php

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Filament\Admin\Widgets\OrderStatusChart;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);
});

describe('OrderStatusChart Widget', function () {
    it('returns chart data structure', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data)->toHaveKey('datasets');
        expect($data)->toHaveKey('labels');
        expect($data['datasets'])->toHaveCount(1);
    });

    it('has correct chart heading', function () {
        $widget = new OrderStatusChart();

        expect($widget->getHeading())->toBe(__('Orders by Status'));
    });

    it('includes all order statuses in labels', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);
        $labels = $data['labels'];

        expect($labels)->toHaveCount(count(OrderStatus::cases()));
    });

    it('counts orders by status', function () {
        Order::factory(3)->create(['status' => OrderStatus::PaymentPending]);
        Order::factory(2)->create(['status' => OrderStatus::Delivered]);

        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);
        $chartData = $data['datasets'][0]['data'];

        expect($chartData)->toHaveCount(count(OrderStatus::cases()));
    });

    it('has correct dataset label', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0]['label'])->toBe(__('Orders'));
    });

    it('dataset has background colors', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0])->toHaveKey('backgroundColor');
        expect($data['datasets'][0]['backgroundColor'])->toHaveCount(5);
    });

    it('dataset has border colors', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0])->toHaveKey('borderColor');
        expect($data['datasets'][0]['borderColor'])->toHaveCount(5);
    });

    it('has correct chart type', function () {
        $widget = new OrderStatusChart();
        $reflection = new ReflectionMethod($widget, 'getType');
        $reflection->setAccessible(true);
        $type = $reflection->invoke($widget);

        expect($type)->toBe('doughnut');
    });
});
