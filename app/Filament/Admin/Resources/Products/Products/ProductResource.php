<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Products;

use App\Filament\Admin\Imports\ProductImporter;
use App\Filament\Admin\Resources\Products\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Products\Pages\ListProducts;
use App\Filament\Admin\Resources\Products\Traits\FormBuilderTrait;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    use FormBuilderTrait;

    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::mainSection(),

                Section::make(__('Category'))
                    ->schema([
                        Select::make('category_id')
                            ->required()
                            ->label(__('Category'))
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->columnSpanFull()
                            ->searchable()
                            ->preload()
                            ->createOptionForm(
                                [
                                    Section::make([
                                        TextInput::make('name')
                                            ->label(__('Name'))
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                        ->columns(2),

                                    FileUpload::make('big_image')
                                        ->label(__('Big image'))
                                        ->required()
                                        ->moveFiles()
                                        ->orientImagesFromExif(false)
                                        ->preserveFilenames()
                                        ->directory('category-images'),
                                ]
                            )
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading(__('Create category'))
                                    ->modalSubmitActionLabel('Create category');
                            })->columnSpan(1),
                    ]),

                self::imagesSection(),

                Section::make(__('Disassemblies'))->schema([
                    Repeater::make('disassemblies')
                        ->label(__('Disassembly'))
                        ->relationship()
                        ->schema([
                            TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->maxLength(255)
                                ->live(),

                            FileUpload::make('main_image')
                                ->label(__('Main image'))
                                ->required()
                                ->reorderable()
                                ->moveFiles()
                                ->orientImagesFromExif(false)
                                ->preserveFilenames()
                                ->directory(config('custom.product-image-storage')),
                        ])
                        ->columns(2)
                        ->collapsed()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(ProductImporter::class),
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Product');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Products');
    }
}
