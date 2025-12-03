<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case Card = 'Card';
    case BankTransfer = 'Bank transfer';
    case Bizum = 'Bizum';
    case PayPal = 'PayPal';

    public function getLabel(): string
    {
        return match ($this) {
            self::BankTransfer => __('Bank transfer'),
            self::Card => __('Card'),
            self::Bizum => __('Bizum'),
            self::PayPal => __('PayPal'),
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::BankTransfer => 'warning',
            self::Card => 'success',
            self::Bizum => Color::generateV3Palette('rgb(8, 131, 135)'),
            self::PayPal => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::BankTransfer => 'heroicon-c-building-office-2',
            self::Card => 'heroicon-s-credit-card',
            self::Bizum => 'heroicon-o-device-phone-mobile',
            self::PayPal => 'ri-paypal-fill',
        };
    }
}
