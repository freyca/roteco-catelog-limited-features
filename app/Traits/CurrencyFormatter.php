<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Support\Number;

trait CurrencyFormatter
{
    public static function formatCurrency(float $value): string
    {
        $locale = config('app.locale');

        if (! is_string($locale)) {
            throw new Exception('Invalid locale configured');
        }

        return strval(
            Number::currency(
                $value,
                in: 'EUR',
                locale: $locale,
            )
        );
    }
}
