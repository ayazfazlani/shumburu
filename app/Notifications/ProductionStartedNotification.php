<?php

namespace App\Notifications;

use App\Models\ProductionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductionStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ProductionOrder $productionOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductionOrder $productionOrder)
    {
        $this->productionOrder = $productionOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Production Started - ' . $this->productionOrder->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Production has started for order #' . $this->productionOrder->order_number)
            ->line('Customer: ' . $this->productionOrder->customer->name)
            ->line('Production Start Date: ' . $this->productionOrder->production_start_date?->format('M d, Y'))
            ->action('View Order Details', route('sales.orders'))
            ->line('Thank you for using our system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Production Started',
            'message' => 'Production has started for order #' . $this->productionOrder->order_number,
            'order_number' => $this->productionOrder->order_number,
            'customer_name' => $this->productionOrder->customer->name,
            'status' => $this->productionOrder->status,
            'production_start_date' => $this->productionOrder->production_start_date?->format('Y-m-d'),
            'total_price' => $this->productionOrder->formatted_total_price,
            'action_url' => route('sales.orders'),
            'icon' => 'fas fa-play-circle',
            'color' => 'green',
        ];
    }
}

