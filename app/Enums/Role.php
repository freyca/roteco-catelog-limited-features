<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasColor, HasIcon, HasLabel
{
    case Customer = 'customer';
    case Admin = 'admin';

    public function getLabel(): string
    {
        return match ($this) {
            self::Customer => __('Customer'),
            self::Admin => __('Admin'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Customer => 'info',
            self::Admin => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Customer => 'heroicon-o-user-circle',
            self::Admin => 'heroicon-s-lock-open',
        };
    }
}
