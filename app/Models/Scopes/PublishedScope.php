<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class PublishedScope implements Scope
{
    /**
     * Limits the products users can see by published status
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::user()?->role !== Role::Admin) {
            $builder->where('published', true);
        }
    }
}
