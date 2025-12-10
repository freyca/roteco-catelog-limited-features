<?php

declare(strict_types=1);

namespace App\Filament\Admin\Imports;

use App\Models\ProductSparePart;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProductSparePartImporter extends Importer
{
    protected static ?string $model = ProductSparePart::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('ean13')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('price_with_discount')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('published')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('disassembly')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ProductSparePart
    {
        if (isset($this->data['id'])) {
            $record = ProductSparePart::find($this->data['id']);
            if ($record) {
                return $record;
            }
        }
        // Fallback to ean13 as unique key
        return ProductSparePart::firstOrNew([
            'ean13' => $this->data['ean13'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product spare part import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
