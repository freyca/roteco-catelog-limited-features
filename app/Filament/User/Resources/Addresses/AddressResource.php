<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Addresses;

use App\Enums\AddressType;
use App\Filament\User\Resources\Addresses\Pages\CreateAddress;
use App\Filament\User\Resources\Addresses\Pages\EditAddress;
use App\Filament\User\Resources\Addresses\Pages\ListAddress;
use App\Models\Address;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        /**
         * @var User
         */
        $user = Auth::user();

        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->default($user->name)
                    ->maxLength(255),
                TextInput::make('surname')
                    ->label(__('Surname'))
                    ->default($user->surname)
                    ->required()
                    ->maxLength(255),
                ToggleButtons::make('address_type')
                    ->label(__('Address type'))
                    ->default(AddressType::Shipping)
                    ->inline()
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('address')
                    ->label(__('Address')),
                TextColumn::make('address_type')
                    ->label(__('Type'))
                    ->badge(),
                TextColumn::make('city')
                    ->label(__('City')),
                TextColumn::make('zip_code')
                    ->label(__('Zip code')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
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
            'create' => CreateAddress::route('/create'),
            'index' => ListAddress::route('/'),
            'edit' => EditAddress::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User');
    }

    public static function getModelLabel(): string
    {
        return __('Shipping address');
    }
}
