<?php

declare(strict_types=1);

namespace App\Filament\Admin\Imports;

use App\Models\Disassembly;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class DisassemblyImporter extends Importer
{
    protected static ?string $model = Disassembly::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('main_image')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('product')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Disassembly
    {
        // Use find for id, fallback to name
        if (isset($this->data['id'])) {
            $record = Disassembly::find($this->data['id']);
            if ($record) {
                return $record;
            }
        }

        return Disassembly::firstOrNew(['name' => $this->data['name']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your disassembly import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
