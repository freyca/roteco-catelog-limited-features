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
            __('Paid') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Paid)), // @phpstan-ignore-line
            __('Payment Failed') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::PaymentFailed)), // @phpstan-ignore-line
            __('Payment Pending') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::PaymentPending)), // @phpstan-ignore-line
            __('Processing') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Processing)), // @phpstan-ignore-line
            __('Shipped') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Shipped)), // @phpstan-ignore-line
            __('Delivered') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Delivered)), // @phpstan-ignore-line
            __('Cancelled') => Tab::make()->query(fn ($query) => $query->where('status', OrderStatus::Cancelled)), // @phpstan-ignore-line
        ];
    }
}
