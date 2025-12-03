<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Events\ProductDeleted;
use App\Models\Scopes\PublishedScope;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([PublishedScope::class])]
class Product extends BaseProduct
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'category_id',
            'can_be_assembled',
            'mandatory_assembly',
            'assembly_price',
        ]);

        $this->mergeCasts([
            'can_be_assembled' => 'boolean',
            'mandatory_assembly' => 'boolean',
            'assembly_price' => MoneyCast::class,
        ]);

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

    /**
     * @return HasMany<ProductVariant, $this>
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getFormattedAssemblyPrice(): string
    {
        return $this->formatCurrency($this->assembly_price);
    }

    public function disassemblies(): HasMany
    {
        return $this->hasMany(Disassembly::class);
    }
}
