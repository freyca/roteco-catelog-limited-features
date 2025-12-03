<?php

declare(strict_types=1);

namespace App\Notifications;

use Filament\Schemas\Schema;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ContactFormNotification extends Notification
{
    use Queueable;

    private string $name;

    private string $email;

    private string $user_message;

    public function __construct(private Schema $form)
    {
        $form_data = $this->form->getState();

        $this->name = strval(data_get($form_data, 'name'));
        $this->email = strval(data_get($form_data, 'email'));
        $this->user_message = strval(data_get($form_data, 'message'));
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Contact form reached'))             // @phpstan-ignore argument.type

            ->line(Lang::get('Name'.': '.$this->name))
            ->line(Lang::get('Email'.': '.$this->email))
            ->line(Lang::get('Message'.': '.$this->user_message));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
