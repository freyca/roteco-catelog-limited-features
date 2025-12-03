<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Products\Pages;

use App\Filament\Admin\Resources\Products\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
