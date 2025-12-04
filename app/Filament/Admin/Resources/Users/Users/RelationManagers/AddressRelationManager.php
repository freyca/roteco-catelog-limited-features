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
use Illuminate\Support\Facades\Auth;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        $user = $this->ownerRecord;

        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->default($user->name)
                    ->maxLength(255),
                TextInput::make('surname')
                    ->label(__('Surname'))
                    ->required()
                    ->default($user->surname)
                    ->maxLength(255),
                Select::make('address_type')
                    ->label(__('Address type'))
                    ->required()
                    ->options(AddressType::class),
                TextInput::make('bussiness_name')
                    ->label(__('Business Name') . ' (' . __('optional') . ')')
                    ->maxLength(255),
                TextInput::make('financial_number')
                    ->label(__('Financial Number') . ' (' . __('optional') . ')')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('Phone'))
                    ->required()
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->required()
                    ->email()
                    ->default($user->email)
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
                CreateAction::make()
                    ->label(__('Create') . ' ' . __('address')),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('Edit')),
                DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
