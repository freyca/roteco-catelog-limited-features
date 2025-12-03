<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Disassemblies;

use App\Filament\Admin\Imports\DisassemblyImporter;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\CreateDisassembly;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\EditDisassembly;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\ListDisassemblies;
use App\Filament\Admin\Resources\Products\Traits\FormBuilderTrait;
use App\Models\Disassembly;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DisassemblyResource extends Resource
{
    use FormBuilderTrait;

    protected static ?string $model = Disassembly::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-wrench-screwdriver';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('id')
                        ->disabled(),

                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                ])->columns(2),

                Section::make(__('Product'))->schema([
                    Select::make('product_id')
                        ->label(__('Product'))
                        ->required()
                        ->relationship(name: 'product', titleAttribute: 'name')
                        ->columnSpanFull()
                        ->searchable()
                        ->preload(),
                ]),

                self::imagesSection(),

                Section::make(__('Spare parts'))->schema([
                    Repeater::make('productSpareParts')
                        ->label(__('Product spare parts'))
                        ->relationship()
                        ->schema([
                            self::mainSection(),

                            self::priceSectionWithParentProduct(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(DisassemblyImporter::class),
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
            'index' => ListDisassemblies::route('/'),
            'edit' => EditDisassembly::route('/{record}/edit'),
            'create' => CreateDisassembly::route('/create'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Disassembly');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Products');
    }
}
