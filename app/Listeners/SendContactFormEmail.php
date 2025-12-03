<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ContactFormSubmitted;
use App\Models\User;
use App\Notifications\ContactFormNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendContactFormEmail implements ShouldQueue
{
    public function handle(ContactFormSubmitted $event): void
    {
        Notification::send(
            User::where('role', 'admin')->first(),     // TODO: define who will receive this notifications
            new ContactFormNotification($event->form)
        );
    }
}
