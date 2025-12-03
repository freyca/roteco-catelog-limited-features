<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('purchase_cost');
        $pendingOrders = Order::where('status', OrderStatus::PaymentPending)->count();
        $completedOrders = Order::where('status', OrderStatus::Delivered)->count();

        return [
            Stat::make(__('Total Orders'), $totalOrders)
                ->description(__('All orders'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make(__('Total Revenue'), '€'.number_format($totalRevenue / 100, 2))
                ->description(__('All time'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Pending Orders'), $pendingOrders)
                ->description(__('Waiting for payment'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Completed Orders'), $completedOrders)
                ->description(__('Successfully delivered'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
