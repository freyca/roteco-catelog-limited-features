<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use App\Models\Traits\HasPriceWhenUserOwnsProduct;
use Database\Factories\ProductSparePartFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy([PublishedScope::class])]
class ProductSparePart extends BaseProduct
{
    /** @use HasFactory<ProductSparePartFactory> */
    use HasFactory;

    use HasPriceWhenUserOwnsProduct;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'disassembly_id',
        ]);

        parent::__construct($attributes);
    }

    // /**
    // * @return BelongsToMany<Product, $this>
    // */
    // public function products(): BelongsToMany
    // {
    //    return $this->belongsToMany(Product::class);
    // }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function disassembly(): BelongsTo
    {
        return $this->belongsTo(Disassembly::class);
    }

    /**
     * @return Collection<int, Category>
     */
    public function categories(): Collection
    {
        return Category::whereIn('id', $this->products->pluck('category_id')->unique())->get();
    }
}
