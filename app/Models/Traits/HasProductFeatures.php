<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\ProductFeature;
use App\Models\ProductFeatureValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProductFeatures
{
    /**
     * @return BelongsToMany<ProductFeatureValue, $this>
     */
    public function productFeatureValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductFeatureValue::class);
    }

    /**
     * @return Collection<int, ProductFeature>
     */
    public function productFeatures(): Collection
    {
        return ProductFeature::whereIn(
            'id',
            $this->productFeatureValues
                ->pluck('product_feature_id')
                ->unique()
        )->get();
    }
}
