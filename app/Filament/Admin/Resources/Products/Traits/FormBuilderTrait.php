<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Traits;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

trait FormBuilderTrait
{
    private static function mainSection(): Section
    {
        return Section::make()->schema([
            Toggle::make('published')
                ->label(__('Visible on shop'))
                ->helperText(__('If off, this product will be hidden from the shop.'))
                ->columnSpan('full')
                ->default(false),

            TextInput::make('id')
                ->disabled(),

            TextInput::make('reference')
                ->label(__('Reference'))
                ->required()
                ->unique()
                ->maxLength(255),

            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),

        ])->columns(2);
    }

    private static function priceSection(): Section
    {
        return Section::make(__('Pricing'))
            ->schema([
                TextInput::make('price')
                    ->label(__('Price PVP'))
                    ->numeric()
                    ->suffix('€')
                    ->required(),

                TextInput::make('price_with_discount')
                    ->label(__('Price to retailer'))
                    ->suffix('€')
                    ->required()
                    ->numeric(),

            ])->columns(3);
    }

    private static function priceSectionWithParentProduct(): Section
    {
        return Section::make(__('Pricing'))
            ->schema([
                TextInput::make('price')
                    ->label(__('Price PVP'))
                    ->numeric()
                    ->suffix('€')
                    ->required(),

                TextInput::make('price_with_discount')
                    ->label(__('Price to retailer'))
                    ->suffix('€')
                    ->required()
                    ->numeric(),

            ])->columns(2);
    }

    private static function dimensionsSection(): Section
    {
        return Section::make(__('Dimensions'))
            ->schema([
                TextInput::make('dimension_length')
                    ->label(__('Length'))
                    ->numeric()
                    ->suffix('cm')
                    ->required(),

                TextInput::make('dimension_width')
                    ->label(__('Width'))
                    ->suffix('cm')
                    ->numeric()
                    ->required(),

                TextInput::make('dimension_height')
                    ->label(__('Height'))
                    ->suffix('cm')
                    ->numeric()
                    ->required(),

                TextInput::make('dimension_weight')
                    ->label(__('Weight'))
                    ->suffix('kg')
                    ->numeric()
                    ->required(),

            ])->columns(4);
    }

    private static function featuresSection(): Section
    {
        return Section::make(__('Features'))
            ->schema([
                Select::make('product_features')
                    ->required()
                    ->label(__('Select features'))
                    ->relationship(name: 'productFeatureValues', titleAttribute: 'name')
                    ->columnSpanFull()
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->createOptionForm([
                        Section::make()->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            RichEditor::make('description')
                                ->required()
                                ->columnSpan('full'),
                        ]),

                    ]),
            ])->columns(1);
    }

    private static function textsSection(): Section
    {
        return Section::make(__('Texts'))->schema([
            RichEditor::make('short_description')
                ->label(__('Short description'))
                ->required()
                ->columnSpan('full'),

            RichEditor::make('description')
                ->label(__('Full Description'))
                ->required()
                ->columnSpan('full'),
        ]);
    }

    private static function imagesSection(): Section
    {
        return Section::make(__('Images'))
            ->schema([
                FileUpload::make('main_image')
                    ->label(__('Main image'))
                    ->required()
                    ->reorderable()
                    ->moveFiles()
                    ->orientImagesFromExif(false)
                    ->preserveFilenames()
                    ->directory(config('custom.product-image-storage')),

            ])->columns(2);
    }

    private static function relatedProductsSection(): Section
    {
        return Section::make(__('Related products'))
            ->schema([
                Select::make('related_products')
                    ->label(__('Select products'))
                    ->required()
                    ->multiple()
                    ->relationship(name: 'products', titleAttribute: 'name')
                    ->columnSpanFull()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
