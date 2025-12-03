<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFeatureValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductFeatureValue extends Model
{
    /** @use HasFactory<ProductFeatureValueFactory> */
    use HasFactory;

    protected $fillable = [
        'feature_id',
        'name',
        'description',
    ];

    /**
     * @return BelongsTo<ProductFeature, $this>
     */
    public function productFeature(): BelongsTo
    {
        return $this->belongsTo(ProductFeature::class);
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @return BelongsToMany<ProductComplement, $this>
     */
    public function productComplements(): BelongsToMany
    {
        return $this->belongsToMany(ProductComplement::class);
    }

    /**
     * @return BelongsToMany<ProductSparePart, $this>
     */
    public function productSpareParts(): BelongsToMany
    {
        return $this->belongsToMany(ProductSparePart::class);
    }

    public static function allProducts(int $id): ?self
    {
        return self::with(
            [
                'products',
                'productSpareParts',
                'productComplements',
            ]
        )->find($id);
    }
}
