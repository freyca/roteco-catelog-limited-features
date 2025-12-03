<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Orders\Pages;

use App\Filament\User\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
