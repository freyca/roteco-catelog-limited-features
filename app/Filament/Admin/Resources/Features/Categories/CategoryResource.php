<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Features\Categories;

use App\Filament\Admin\Imports\CategoryImporter;
use App\Filament\Admin\Resources\Features\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Features\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Features\Categories\Pages\ListCategories;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('id')
                        ->disabled(),
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->disabled(),
                    // Forms\Components\TextInput::make('meta_description')
                    //    ->label(__('Meta description'))
                    //    ->required()
                    //    ->columnSpanFull()
                    //    ->maxLength(255),
                    // TiptapEditor::make('description')
                    //    ->label(__('Description'))
                    //    ->required()
                    //    ->columnSpanFull(),
                ])->columns(2),

                FileUpload::make('big_image')
                    ->label(__('Big image'))
                    ->required()
                    ->moveFiles()
                    ->preserveFilenames()
                    ->orientImagesFromExif(false)
                    ->directory(config('custom.category-image-storage')),
                // Forms\Components\FileUpload::make('small_image')
                //    ->label(__('Small image'))
                //    ->required()
                //    ->moveFiles()
                //    ->preserveFilenames()
                //    ->orientImagesFromExif(false)
                //    ->directory('category-images'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(CategoryImporter::class),
            ])
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                ImageColumn::make('small_image')
                    ->circular()
                    ->label(__('Image')),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Features');
    }

    public static function getModelLabel(): string
    {
        return __('Categories');
    }
}
