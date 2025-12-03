<?php

declare(strict_types=1);

namespace App\Livewire\Buttons\Traits;

use App\Services\Cart;
use Filament\Notifications\Notification;

trait HasCartInteractions
{
    public int $productQuantity;

    public function add(Cart $cart): void
    {
        if ($cart->add($this->product, 1, $this->assembly_status, $this->variant ?? null)) {
            Notification::make()->title(__('Product added correctly'))->success()->send();
        } else {
            Notification::make()->title(__('Failed to add product'))->danger()->send();
        }

        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant ?? null);

        $this->dispatch('refresh-cart');
    }

    public function increment(Cart $cart): void
    {
        if ($cart->add(
            product: $this->product,
            quantity: 1,
            assemble: $this->assembly_status,
            variant: $this->variant ?? null,
        )) {
            Notification::make()->title(__('Product incremented'))->success()->send();
        } else {
            Notification::make()->title(__('Not enough stock'))->danger()->send();
        }

        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant ?? null);

        $this->dispatch('refresh-cart');
    }

    public function decrement(Cart $cart): void
    {
        $cart->add(
            product: $this->product,
            quantity: -1,
            assemble: $this->assembly_status,
            variant: $this->variant ?? null,
        );

        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant ?? null);

        Notification::make()->title(__('Product decremented'))->danger()->send();

        $this->dispatch('refresh-cart');
    }

    public function remove(Cart $cart): void
    {
        $cart->remove(
            product: $this->product,
            assemble: $this->assembly_status,
            variant: $this->variant ?? null,
        );

        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant ?? null);

        Notification::make()->title(__('Product removed from cart'))->danger()->send();

        $this->dispatch('refresh-cart');
    }
}
