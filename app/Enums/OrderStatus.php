<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case PaymentFailed = 'payment failed';
    case PaymentPending = 'payment pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::Paid => __('Paid'),
            self::Processing => __('Processing'),
            self::Shipped => __('Shipped'),
            self::Delivered => __('Delivered'),
            self::Cancelled => __('Cancelled'),
            self::PaymentFailed => __('Payment Failed'),
            self::PaymentPending => __('Payment Pending'),
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::Paid => Color::generateV3Palette('rgb(187, 247, 208)'),
            self::Processing => Color::generateV3Palette('rgb(74, 222, 128)'),
            self::Shipped => Color::generateV3Palette('rgb(22, 163, 74)'),
            self::Delivered => Color::generateV3Palette('rgb(22, 101, 52)'),
            self::Cancelled => 'gray',
            self::PaymentFailed => 'danger',
            self::PaymentPending => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Paid => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Shipped => 'heroicon-m-truck',
            self::Delivered => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
            self::PaymentFailed => 'heroicon-o-exclamation-circle',
            self::PaymentPending => 'heroicon-o-arrow-right-on-rectangle',
        };
    }
}
