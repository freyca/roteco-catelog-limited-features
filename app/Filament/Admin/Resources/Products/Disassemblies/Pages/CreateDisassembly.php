<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Disassemblies\Pages;

use App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDisassembly extends CreateRecord
{
    protected static string $resource = DisassemblyResource::class;
}
