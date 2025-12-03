<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Features\Categories\Pages;

use App\Filament\Admin\Resources\Features\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
