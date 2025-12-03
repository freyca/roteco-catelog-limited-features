<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\ProductSpareParts;

use App\Filament\Admin\Imports\ProductSparePartImporter;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\CreateProductSparePart;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\EditProductSparePart;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\ListProductSpareParts;
use App\Filament\Admin\Resources\Products\Traits\FormBuilderTrait;
use App\Models\ProductSparePart;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductSparePartResource extends Resource
{
    use FormBuilderTrait;

    protected static ?string $model = ProductSparePart::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-wrench';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::mainSection(),

                Section::make(__('Disassembly'))
                    ->schema([
                        Select::make('disassembly_id')
                            ->required()
                            ->label(__('Disassembly'))
                            ->relationship(name: 'disassembly', titleAttribute: 'name')
                            ->columnSpanFull()
                            ->searchable()
                            ->preload(),
                    ]),

                self::priceSectionWithParentProduct(),

                // self::dimensionsSection(),

                // self::featuresSection(),

                // self::relatedProductsSection(),

                // self::textsSection(),

                // self::imagesSection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(ProductSparePartImporter::class),
            ])
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                ImageColumn::make('main_image')
                    ->circular()
                    ->label(__('Image')),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('Price'))
                    ->badge()
                    ->money(
                        currency: 'eur',
                        locale: 'es'
                    )
                    ->sortable(),

                TextColumn::make('price_with_discount')
                    ->label(__('Price with discount'))
                    ->badge()
                    ->money(
                        currency: 'eur',
                        locale: 'es'
                    )
                    ->sortable(),

                IconColumn::make('published')
                    ->label(__('Published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->sortable()
                    ->date()
                    ->label(__('Creation date')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductSpareParts::route('/'),
            'create' => CreateProductSparePart::route('/create'),
            'edit' => EditProductSparePart::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Products');
    }

    public static function getModelLabel(): string
    {
        return __('Spare parts');
    }
}
