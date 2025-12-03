<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductFeatureFamily;
use Database\Factories\ProductFeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductFeature extends Model
{
    /** @use HasFactory<ProductFeatureFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'family',
        'description',
    ];

    protected $casts = [
        'family' => ProductFeatureFamily::class,
    ];

    /**
     * @return HasMany<ProductFeatureValue, $this>
     */
    public function productFeatureValues(): HasMany
    {
        return $this->hasMany(ProductFeatureValue::class);
    }
}
