<?php

declare(strict_types=1);

namespace App\Filament\Admin\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('purchase_cost')
                ->label(__('Purchase cost')),
            ExportColumn::make('payment_method')
                ->label(__('Payment method')),
            ExportColumn::make('status')
                ->label(__('Status')),
            ExportColumn::make('user.name')
                ->label(__('Name')),
            ExportColumn::make('user.email')
                ->label(__('Email')),
            ExportColumn::make('shippingAddress.address'),
            ExportColumn::make('billingAddress.address'),
            ExportColumn::make('payment_gateway_response'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
