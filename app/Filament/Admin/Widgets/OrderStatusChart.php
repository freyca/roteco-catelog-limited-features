<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected ?string $heading = '';

    protected string $color = 'info';

    public function getHeading(): ?string
    {
        return __('Orders by Status');
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        foreach (OrderStatus::cases() as $status) {
            $count = Order::where('status', $status)->count();
            $data[] = $count;
            $labels[] = $status->getLabel();
        }

        return [
            'datasets' => [
                [
                    'label' => __('Orders'),
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
