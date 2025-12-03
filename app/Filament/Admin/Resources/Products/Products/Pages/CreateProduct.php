<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Products\Pages;

use App\Filament\Admin\Resources\Products\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
