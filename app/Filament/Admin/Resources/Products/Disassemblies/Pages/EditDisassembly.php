<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Disassemblies\Pages;

use App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisassembly extends EditRecord
{
    protected static string $resource = DisassemblyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
