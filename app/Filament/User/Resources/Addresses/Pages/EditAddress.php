<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Addresses\Pages;

use App\Filament\User\Resources\Addresses\AddressResource;
use Filament\Resources\Pages\EditRecord;

class EditAddress extends EditRecord
{
    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
