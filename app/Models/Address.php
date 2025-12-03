<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\Role;
use App\Models\Scopes\AddressScope;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

#[ScopedBy([AddressScope::class])]
class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_type',
        'name',
        'surname',
        'bussiness_name',
        'email',
        'financial_number',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    protected $casts = [
        'address_type' => AddressType::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Address $address) {
            /** @var ?User $user */
            $user = Auth::getUser();

            match (true) {
                $user === null => true,
                $user->role === Role::Admin => true,
                default => $address->user_id = $user->id,
            };
        });
    }
}
