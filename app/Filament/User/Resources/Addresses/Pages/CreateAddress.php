<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Addresses\Pages;

use App\Filament\User\Resources\Addresses\AddressResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAddress extends CreateRecord
{
    protected static string $resource = AddressResource::class;
}
