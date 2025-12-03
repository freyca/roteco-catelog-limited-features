<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Casts\MoneyCast;

trait HasPriceWhenUserOwnsProduct
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable(['price_when_user_owns_product']);

        $this->mergeCasts([
            'price_when_user_owns_product' => MoneyCast::class,
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        $parent_casts = parent::casts();

        return array_merge($parent_casts, [
            'price_when_user_owns_product' => MoneyCast::class,
        ]);
    }

    public function getFormattedPriceWhenUserOwnsProduct(): string
    {
        $price_when_user_owns_product = $this->price_when_user_owns_product === null ? floatval(0) : $this->price_when_user_owns_product;

        return $this->formatCurrency($price_when_user_owns_product);
    }
}
