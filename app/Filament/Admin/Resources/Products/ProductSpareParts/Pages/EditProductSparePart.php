<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\ProductSpareParts\Pages;

use App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductSparePart extends EditRecord
{
    protected static string $resource = ProductSparePartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
