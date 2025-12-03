<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\ProductSpareParts\Pages;

use App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductSpareParts extends ListRecords
{
    protected static string $resource = ProductSparePartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
