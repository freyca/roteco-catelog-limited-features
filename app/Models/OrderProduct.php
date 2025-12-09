<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use Database\Factories\OrderProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Pivot
{
    /** @use HasFactory<OrderProductFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'order_product';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => MoneyCast::class,
        ];
    }

    protected $fillable = [
        'order_id',
        'orderable_id',
        'orderable_type',
        'unit_price',
        'quantity',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }
}
