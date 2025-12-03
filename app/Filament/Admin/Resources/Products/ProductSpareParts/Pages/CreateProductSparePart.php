<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\ProductSpareParts\Pages;

use App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductSparePart extends CreateRecord
{
    protected static string $resource = ProductSparePartResource::class;
}
