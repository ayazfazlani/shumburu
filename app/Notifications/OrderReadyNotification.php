<?php

namespace App\Notifications;

use App\Models\ProductionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyNotification extends Notification implements ShouldQueue
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
            ->subject("🚨 URGENT: Order Ready for Delivery - #{$this->productionOrder->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("🚨 **URGENT NOTIFICATION** 🚨")
            ->line("Production order #{$this->productionOrder->order_number} is now **READY FOR DELIVERY**!")
            ->line("**This requires immediate attention from the sales team.**")
            ->line("**Order Details:**")
            ->line("• Order Number: #{$this->productionOrder->order_number}")
            ->line("• Customer: {$this->productionOrder->customer->name}")
            ->line("• Status: ✅ READY FOR DELIVERY")
            ->line("• Total Value: {$this->productionOrder->formatted_total_price}")
            ->when($this->productionOrder->production_end_date, function ($message) {
                return $message->line("• Production Completed: {$this->productionOrder->production_end_date->format('M d, Y')}");
            })
            ->action('View Order Details & Schedule Delivery', $url)
            ->line('⚠️ **ACTION REQUIRED:** Please coordinate with the delivery team immediately to schedule customer delivery.')
            ->line('This order is ready and waiting for delivery coordination.')
            ->salutation('Best regards, Shumburo CRM System');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'order_ready',
            'title' => '🚨 URGENT: Order Ready for Delivery',
            'message' => "🚨 URGENT: Order #{$this->productionOrder->order_number} is ready for delivery to {$this->productionOrder->customer->name}. Immediate action required!",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'production_end_date' => $this->productionOrder->production_end_date?->toDateString(),
                'urgency' => 'high',
                'priority' => 'urgent',
            ],
            'action_url' => '/sales/orders',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'red',
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
            'type' => 'order_ready',
            'title' => 'Order Ready for Delivery',
            'message' => "Order #{$this->productionOrder->order_number} is ready for delivery to {$this->productionOrder->customer->name}",
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'total_price' => $this->productionOrder->total_price,
                'production_end_date' => $this->productionOrder->production_end_date?->toDateString(),
            ],
        ];
    }
}
