<?php

use App\Enums\Role;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);
});

describe('RevenueChart Widget', function () {
    it('returns chart data structure', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data)->toHaveKey('datasets');
        expect($data)->toHaveKey('labels');
        expect($data['datasets'])->toHaveCount(1);
    });

    it('has correct chart heading', function () {
        $widget = new RevenueChart;

        expect($widget->getHeading())->toBe(__('Revenue (Last 7 Days)'));
    });

    it('includes last 7 days labels', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['labels'])->toHaveCount(7);
    });

    it('calculates revenue for each day', function () {
        $today = Carbon::now();
        Order::factory()->create([
            'purchase_cost' => 10000,
            'created_at' => $today->copy(),
        ]);
        Order::factory()->create([
            'purchase_cost' => 5000,
            'created_at' => $today->copy(),
        ]);

        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);
        $dataset = $data['datasets'][0];

        // Last element (index 6) should be today's revenue
        // The data array has values in cents before division, so 15000 cents
        expect($dataset['data'][6])->toBeGreaterThanOrEqual(150);
    });

    it('has correct dataset label', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0]['label'])->toBe(__('Revenue (€)'));
    });

    it('dataset is filled', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0]['fill'])->toBeTrue();
    });

    it('dataset has border color', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0])->toHaveKey('borderColor');
        expect($data['datasets'][0]['borderColor'])->toBe('rgb(34, 197, 94)');
    });

    it('dataset has background color', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getData');
        $reflection->setAccessible(true);
        $data = $reflection->invoke($widget);

        expect($data['datasets'][0])->toHaveKey('backgroundColor');
        expect($data['datasets'][0]['backgroundColor'])->toBe('rgba(34, 197, 94, 0.1)');
    });

    it('has correct chart type', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getType');
        $reflection->setAccessible(true);
        $type = $reflection->invoke($widget);

        expect($type)->toBe('line');
    });

    it('chart options are present', function () {
        $widget = new RevenueChart;
        $reflection = new ReflectionMethod($widget, 'getOptions');
        $reflection->setAccessible(true);
        $options = $reflection->invoke($widget);

        expect($options)->not()->toBeNull();
        expect($options)->toHaveKey('plugins');
    });
});
