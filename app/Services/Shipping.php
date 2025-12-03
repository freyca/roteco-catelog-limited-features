<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Shipping\ShippingRepositoryInterface;

final class Shipping
{
    public function __construct(private readonly ShippingRepositoryInterface $repository) {}

    public function getTrackStatus(): string
    {
        return $this->repository->getTrackStatus();
    }

    public function isShipped(): bool
    {
        return $this->repository->isShipped();
    }

    public function getTrackInformationUrl(): string
    {
        return $this->repository->getTrackInformationUrl();
    }
}
