<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Features\Categories\Pages;

use App\Filament\Admin\Resources\Features\Categories\CategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
