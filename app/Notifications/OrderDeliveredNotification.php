<?php

namespace App\Notifications;

use App\Models\ProductionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification implements ShouldQueue
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
        $url = url('/sales/orders');
        
        return (new MailMessage)
            ->subject("🚚 Order Delivered: #{$this->productionOrder->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Order #{$this->productionOrder->order_number} has been successfully delivered to the customer.")
            ->line("**Order Details:**")
            ->line("• Order Number: #{$this->productionOrder->order_number}")
            ->line("• Customer: {$this->productionOrder->customer->name}")
            ->line("• Status: Delivered")
            ->line("• Total Value: {$this->productionOrder->formatted_total_price}")
            ->when($this->productionOrder->delivery_date, function ($message) {
                return $message->line("• Delivery Date: {$this->productionOrder->delivery_date->format('M d, Y')}");
            })
            ->action('View Order Details', $url)
            ->line('The order has been completed successfully. You may follow up with the customer for feedback.')
            ->salutation('Best regards, Shumburo CRM System');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'order_delivered',
            'title' => 'Order Delivered Successfully',
            'message' => "Order #{$this->productionOrder->order_number} has been delivered to {$this->productionOrder->customer->name}",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'delivery_date' => $this->productionOrder->delivery_date?->toDateString(),
            ],
            'action_url' => '/sales/orders',
            'icon' => 'fas fa-truck',
            'color' => 'green',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_delivered',
            'title' => 'Order Delivered Successfully',
            'message' => "Order #{$this->productionOrder->order_number} has been delivered to {$this->productionOrder->customer->name}",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'delivery_date' => $this->productionOrder->delivery_date?->toDateString(),
            ],
        ];
    }
}
