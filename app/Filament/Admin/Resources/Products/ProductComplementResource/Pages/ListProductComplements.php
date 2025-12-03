<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\ProductComplementResource\Pages;

use App\Filament\Admin\Resources\Products\ProductComplementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductComplements extends ListRecords
{
    protected static string $resource = ProductComplementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
