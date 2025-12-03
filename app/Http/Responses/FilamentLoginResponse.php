<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Enums\Role;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLoginResponse implements LoginResponse
{
    public function toResponse($request): Redirector|RedirectResponse
    {
        if (Auth::user()?->role === Role::Admin) {
            return redirect('/admin');
        }

        return redirect('/');
    }
}
