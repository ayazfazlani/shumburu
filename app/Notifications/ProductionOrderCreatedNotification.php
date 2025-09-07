<?php

namespace App\Notifications;

use App\Models\ProductionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ProductionOrderCreatedNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/sales/orders');
        
        return (new MailMessage)
            ->subject("🚀 New Production Order: #{$this->productionOrder->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new production order has been created and requires your attention.")
            ->line("**Order Details:**")
            ->line("• Order Number: #{$this->productionOrder->order_number}")
            ->line("• Customer: {$this->productionOrder->customer->name}")
            ->line("• Requested Date: {$this->productionOrder->requested_date->format('M d, Y')}")
            ->line("• Total Value: {$this->productionOrder->formatted_total_price}")
            ->line("• Status: " . ucfirst($this->productionOrder->status))
            ->when($this->productionOrder->notes, function ($message) {
                return $message->line("• Notes: {$this->productionOrder->notes}");
            })
            ->action('View Order Details', $url)
            ->line('Please review and take necessary action.')
            ->salutation('Best regards, Shumburo CRM System');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'production_order_created',
            'title' => 'New Production Order Created',
            'message' => "Production order #{$this->productionOrder->order_number} has been created for {$this->productionOrder->customer->name}",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'requested_date' => $this->productionOrder->requested_date->toDateString(),
            ],
            'action_url' => '/sales/orders',
            'icon' => 'fas fa-clipboard-list',
            'color' => 'blue',
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
            'type' => 'production_order_created',
            'title' => 'New Production Order Created',
            'message' => "Production order #{$this->productionOrder->order_number} has been created for {$this->productionOrder->customer->name}",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'requested_date' => $this->productionOrder->requested_date->toDateString(),
            ],
        ];
    }
}
