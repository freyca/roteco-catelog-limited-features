<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\Role;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AddressScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder<Address>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var ?User $user */
        $user = Auth::getUser();

        match (true) {
            $user === null => true,
            $user->role === Role::Admin => true,
            default => $builder->where('user_id', $user->id),
        };
    }
}
