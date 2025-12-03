<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\OrderStatsOverview;
use App\Filament\Admin\Widgets\OrderStatusChart;
use App\Filament\Admin\Widgets\RevenueChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '';

    public function getTitle(): string
    {
        return __('Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            OrderStatsOverview::class,
            RevenueChart::class,
            OrderStatusChart::class,
        ];
    }
}
