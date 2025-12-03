<?php

declare(strict_types=1);

namespace App\DTO;

class FilterDTO
{
    private int $min_price;

    private int $max_price;

    private int $category;

    /**
     * @var array<int>
     */
    private array $features = [];

    public function minPrice(int $price): void
    {
        $this->min_price = $price;
    }

    public function maxPrice(int $price): void
    {
        $this->max_price = $price;
    }

    public function category(int $category): void
    {
        $this->category = $category;
    }

    /**
     * @param  array<int>  $features
     */
    public function features(array $features): void
    {
        foreach ($features as $featureId) {
            array_push($this->features, intval($featureId));
        }
    }

    public function getMinPrice(): int
    {
        return $this->min_price;
    }

    public function getMaxPrice(): int
    {
        return $this->max_price;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function getfeatures(): array
    {
        return $this->features;
    }
}
