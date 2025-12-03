<?php

declare(strict_types=1);

namespace Database\Traits;

trait WithProductDiscounts
{
    private function isProductDiscounted(int $actual_price): ?float
    {
        if (fake()->randomNumber(1) % 2 === 0) {
            return fake()->randomFloat(2, 5, $actual_price - 2);
        }

        return null;
    }
}
