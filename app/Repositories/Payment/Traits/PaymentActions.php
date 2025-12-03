<?php

declare(strict_types=1);

namespace App\Repositories\Payment\Traits;

trait PaymentActions
{
    protected function convertPriceToCents(float $price): int
    {
        return intval(bcmul(strval($price), '100'));
    }
}
