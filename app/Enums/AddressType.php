<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AddressType: string implements HasColor, HasIcon, HasLabel
{
    case Billing = 'billing';
    case Shipping = 'shipping';
    case ShippingAndBilling = 'shipping and billing';

    public function getLabel(): string
    {
        return match ($this) {
            self::Billing => __('Billing'),
            self::Shipping => __('Shipping'),
            self::ShippingAndBilling => __('Shipping and Billing')
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Billing => 'success',
            self::Shipping => 'info',
            self::ShippingAndBilling => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Billing => 'heroicon-c-document-currency-euro',
            self::Shipping => 'heroicon-o-truck',
            self::ShippingAndBilling => 'heroicon-m-rocket-launch',
        };
    }
}
