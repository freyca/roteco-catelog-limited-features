<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(private Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->load('user', 'shippingAddress', 'billingAddress');

        // Load orderProducts with orderable relationship, bypassing PublishedScope
        // If we respect the scope, a product could be missing from the email
        $order->orderProducts = $order->orderProducts()
            ->with(['orderable' => fn ($query) => $query->withoutGlobalScopes()])
            ->get();

        return (new MailMessage)
            ->subject(__('Order Confirmation'))
            ->greeting(__('Hello :name', ['name' => $order->user->name]))
            ->line(__('Thank you for your order!'))
            ->line(__('Order ID').': '.$order->id)
            ->line(__('Order Status').': '.$order->status->getLabel())
            ->line(__('Total Amount').': €'.number_format($order->purchase_cost / 100, 2))
            ->line(__('Products'.':'))
            ->with('products', $order->orderProducts)
            ->markdown('emails.order-confirmation', [
                'order' => $order,
                'products' => $order->orderProducts,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
        ];
    }
}
