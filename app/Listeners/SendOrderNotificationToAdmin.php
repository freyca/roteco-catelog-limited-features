<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class SendOrderNotificationToAdmin implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $admin_notification = User::where('email', config('custom.admin_email'))->first();

        if (! $admin_notification) {
            throw new RuntimeException('No admin user found. Please ensure admin user exists to be notified.');
        }

        Notification::send($admin_notification, new AdminOrderNotification($event->order));
    }
}
