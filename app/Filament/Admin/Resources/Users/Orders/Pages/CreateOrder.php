<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Orders\Pages;

use App\Filament\Admin\Resources\Users\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
