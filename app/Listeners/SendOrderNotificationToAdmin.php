<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\AdminOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class SendOrderNotificationToAdmin implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $admin_notification_email = config('custom.mail_admin_address');

        if (is_null($admin_notification_email)) {
            throw new RuntimeException('No admin users found. Please ensure at least one admin user exists in the system.');
        }

        Notification::route('mail', $admin_notification_email)
            ->notify(new AdminOrderNotification($event->order));
    }
}
