<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Traits\CurrencyFormatter;

trait FormatsPrices
{
    use CurrencyFormatter;

    public function getFormattedPrice(): string
    {
        $price = $this->price ?? floatval(0);

        return $this->formatCurrency($price);
    }

    public function getFormattedPriceWithDiscount(): string
    {
        $price_with_discount = $this->price_with_discount === null ? floatval(0) : $this->price_with_discount;

        return $this->formatCurrency($price_with_discount);
    }

    public function getFormattedSavings(): string
    {
        $savings = floatval($this->price - $this->price_with_discount);

        return $this->formatCurrency($savings);
    }
}
