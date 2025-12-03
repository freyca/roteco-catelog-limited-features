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
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            throw new RuntimeException('No admin users found. Please ensure at least one admin user exists in the system.');
        }

        Notification::send($admins, new AdminOrderNotification($event->order));
    }
}
