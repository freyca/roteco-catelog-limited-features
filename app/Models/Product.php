<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\ProductDeleted;
use App\Models\Scopes\PublishedScope;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([PublishedScope::class])]
class Product extends BaseProduct
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'category_id',
        ]);

        $this->mergeCasts([]);

        parent::__construct($attributes);
    }

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'deleting' => ProductDeleted::class,
    ];

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<ProductSparePart, $this>
     */
    public function productSpareParts(): BelongsToMany
    {
        return $this->belongsToMany(ProductSparePart::class);
    }

    public function disassemblies(): HasMany
    {
        return $this->hasMany(Disassembly::class);
    }
}
