<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disassembly extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'product_id',
        'main_image',
    ];

    public function productSpareParts(): HasMany
    {
        return $this->hasMany(ProductSparePart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
