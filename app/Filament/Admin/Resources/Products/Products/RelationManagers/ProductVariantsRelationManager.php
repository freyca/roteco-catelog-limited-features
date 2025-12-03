<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Products\RelationManagers;

use App\Filament\Admin\Resources\Products\Traits\FormBuilderTrait;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProductVariantsRelationManager extends RelationManager
{
    use FormBuilderTrait;

    protected static string $relationship = 'productVariants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ean13')
                    ->label(__('Ean13'))
                    ->required()
                    ->numeric(),

                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),

                self::priceSection(),

                self::featuresSection(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(__('Product variants'))
            ->columns([
                TextColumn::make('ean13')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money(
                        currency: 'eur',
                        locale: 'es'
                    )
                    ->badge()
                    ->sortable(),
                TextColumn::make('price_with_discount')
                    ->label(__('Price with discount'))
                    ->money(
                        currency: 'eur',
                        locale: 'es'
                    )
                    ->badge()
                    ->sortable(),
                TextColumn::make('productFeatureValues.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Product feature values')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('Product variant');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Product variants');
    }
}
