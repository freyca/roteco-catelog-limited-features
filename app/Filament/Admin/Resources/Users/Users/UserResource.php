<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Users;

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Users\RelationManagers\AddressRelationManager;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('id')
                        ->disabled()
                        ->label('ID')
                        ->columnSpanFull(),
                    // Forms\Components\ToggleButtons::make('role')
                    //    ->inline()
                    //    ->required()
                    //    ->options(Role::class),
                    TextInput::make('name')
                        ->required()
                        ->label(__('Name')),
                    TextInput::make('surname')
                        ->required()
                        ->label(__('Surname')),
                    TextInput::make('email')
                        ->required()
                        ->email(),
                    TextInput::make('password')
                        ->label(__('Password'))
                        ->password(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('surname')
                    ->label(__('Surname'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label(__('Role'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->date()
                    ->label(__('Registered date')),
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
            AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Users');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }
}
