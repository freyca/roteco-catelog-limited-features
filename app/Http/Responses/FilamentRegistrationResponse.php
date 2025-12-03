<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Services\Cart;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentRegistrationResponse implements RegistrationResponse
{
    public function toResponse($request): Redirector|RedirectResponse
    {
        /** @var Cart */
        $cart = app(Cart::class);

        if ($cart->getTotalQuantity() > 0) {
            return redirect()->route('checkout.cart');
        }

        return redirect('/user');
    }
}
