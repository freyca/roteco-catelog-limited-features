<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectsAdminUsersToAdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?User */
        $user = Auth::getUser();

        return match (true) {
            $user === null => $next($request),
            $user->role === Role::Admin => redirect('/admin'),
            default => $next($request),
        };
    }
}
