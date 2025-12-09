<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\Users\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('All')),
            __('Paid') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Paid)),
            __('Payment Failed') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::PaymentFailed)),
            __('Payment Pending') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::PaymentPending)),
            __('Processing') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Processing)),
            __('Shipped') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Shipped)),
            __('Delivered') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Delivered)),
            __('Cancelled') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Cancelled)),
        ];
    }
}
