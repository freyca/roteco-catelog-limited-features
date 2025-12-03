<?php

declare(strict_types=1);

namespace App\Repositories\Shipping;

interface ShippingRepositoryInterface
{
    public function getTrackStatus(): string;

    public function isShipped(): bool;

    public function getTrackInformationUrl(): string;
}
