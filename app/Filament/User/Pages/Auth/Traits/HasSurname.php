<?php

declare(strict_types=1);

namespace App\Filament\User\Pages\Auth\Traits;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

trait HasSurname
{
    protected function getSurNameFormComponent(): Component
    {
        return TextInput::make('surname')
            ->label(__('Surname'))
            ->required()
            ->maxLength(255);
    }
}
