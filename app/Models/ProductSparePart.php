<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Database\Factories\ProductSparePartFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([PublishedScope::class])]
class ProductSparePart extends BaseProduct
{
    /** @use HasFactory<ProductSparePartFactory> */
    use HasFactory;

    use SoftDeletes;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'disassembly_id',
        ]);

        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function disassembly(): BelongsTo
    {
        return $this->belongsTo(Disassembly::class);
    }
}
