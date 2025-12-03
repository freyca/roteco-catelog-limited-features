<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Disassemblies\Pages;

use App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisassemblies extends ListRecords
{
    protected static string $resource = DisassemblyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
