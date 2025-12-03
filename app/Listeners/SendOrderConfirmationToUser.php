<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmationToUser implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $event->order->user->notify(new OrderConfirmationNotification($event->order));
    }
}
