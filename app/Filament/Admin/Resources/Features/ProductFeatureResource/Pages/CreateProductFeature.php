<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Features\ProductFeatureResource\Pages;

use App\Filament\Admin\Resources\Features\ProductFeatureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductFeature extends CreateRecord
{
    protected static string $resource = ProductFeatureResource::class;
}
