<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Users\RelationManagers;

use App\Enums\AddressType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('surname')
                    ->label(__('Surname'))
                    ->required()
                    ->maxLength(255),
                Select::make('address_type')
                    ->label(__('Address type'))
                    ->required()
                    ->options(AddressType::class),
                TextInput::make('bussiness_name')
                    ->label(__('Business Name').'(optional)')
                    ->maxLength(255),
                TextInput::make('financial_number')
                    ->label(__('Financial Number').'(optional)')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('Phone'))
                    ->integer()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label(__('Address'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('city')
                    ->label(__('City'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('state')
                    ->label(__('State'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->label(__('Zip code'))
                    ->required()
                    ->numeric()
                    ->integer()
                    ->maxLength(255),
                TextInput::make('country')
                    ->label(__('Country'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                TextColumn::make('address')->label(__('Address')),
                TextColumn::make('city')->label(__('City')),
                TextColumn::make('zip_code')->label(__('Zip code')),
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
}
