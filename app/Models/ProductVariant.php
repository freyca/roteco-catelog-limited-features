<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\FormatsPrices;
use App\Models\Traits\HasProductFeatures;
use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use FormatsPrices;

    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    use HasProductFeatures;

    protected $fillable = [
        'name',
        'product_id',
        'ean13',
        'price',
        'price_with_discount',
        'stock',
    ];

    /**
     * Product variant does not has a slug, it inherits parent Product slug
     * We override the trait so it does not interferes when saving to db
     */
    public static function bootHasSlug(): void {}

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
