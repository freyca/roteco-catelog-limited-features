<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductFeatureFamily: string implements HasColor, HasIcon, HasLabel
{
    case GeneralDetails = 'General details';
    case EngineDetails = 'Engine details';
    case Transmission = 'Transmission';
    case WorkSystem = 'Work system';

    public function getLabel(): string
    {
        return match ($this) {
            self::GeneralDetails => __('General details'),
            self::EngineDetails => __('Engine details'),
            self::Transmission => __('Transmission'),
            self::WorkSystem => __('Work system'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::GeneralDetails => 'info',
            self::EngineDetails => 'danger',
            self::Transmission => 'warning',
            self::WorkSystem => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::GeneralDetails => 'heroicon-c-squares-2x2',
            self::EngineDetails => 'heroicon-s-globe-alt',
            self::Transmission => 'heroicon-o-cube-transparent',
            self::WorkSystem => 'heroicon-s-wrench-screwdriver',
        };
    }
}
