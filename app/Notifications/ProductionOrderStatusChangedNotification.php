<?php

namespace App\Notifications;

use App\Models\ProductionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ProductionOrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ProductionOrder $productionOrder;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductionOrder $productionOrder, string $oldStatus, string $newStatus)
    {
        $this->productionOrder = $productionOrder;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusEmojis = [
            'pending' => '⏳',
            'pending_production' => '⏳',
            'approved' => '✅',
            'in_production' => '🏭',
            'completed' => '🎉',
            'delivered' => '🚚',
        ];

        $statusMessages = [
            'pending' => 'is pending approval',
            'pending_production' => 'is pending production start',
            'approved' => 'has been approved and is ready for production',
            'in_production' => '🚨 PRODUCTION HAS STARTED 🚨',
            'completed' => '🎉 PRODUCTION HAS BEEN COMPLETED 🎉',
            'delivered' => 'has been delivered to the customer',
        ];

        // Make critical status changes more prominent
        $isCriticalStatus = in_array($this->newStatus, ['in_production', 'completed']);
        $subjectPrefix = $isCriticalStatus ? '🚨 URGENT: ' : '';

        $emoji = $statusEmojis[$this->newStatus] ?? '📋';
        $message = $statusMessages[$this->newStatus] ?? 'status has been updated';

        return (new MailMessage)
            ->subject("{$subjectPrefix}{$emoji} Production Order Update: #{$this->productionOrder->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Production order #{$this->productionOrder->order_number} {$message}.")
            ->when($isCriticalStatus, function ($message) {
                return $message->line("⚠️ **This is a critical status change that requires immediate attention!**");
            })
            ->line("**Order Details:**")
            ->line("• Order Number: #{$this->productionOrder->order_number}")
            ->line("• Customer: {$this->productionOrder->customer->name}")
            ->line("• Previous Status: " . ucfirst($this->oldStatus))
            ->line("• Current Status: " . ucfirst($this->newStatus))
            ->line("• Total Value: {$this->productionOrder->formatted_total_price}")
            ->when($this->productionOrder->production_start_date, function ($message) {
                return $message->line("• Production Start: {$this->productionOrder->production_start_date->format('M d, Y')}");
            })
            ->when($this->productionOrder->production_end_date, function ($message) {
                return $message->line("• Production End: {$this->productionOrder->production_end_date->format('M d, Y')}");
            })
            ->when($this->productionOrder->delivery_date, function ($message) {
                return $message->line("• Delivery Date: {$this->productionOrder->delivery_date->format('M d, Y')}");
            })
            ->action('View Order Details', $url)
            ->line('Thank you for using Shumburo CRM System!')
            ->salutation('Best regards, Shumburo CRM System');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $statusColors = [
            'pending' => 'yellow',
            'pending_production' => 'orange',
            'approved' => 'green',
            'in_production' => 'red', // Critical status - red for urgency
            'completed' => 'purple', // Critical status - purple for completion
            'delivered' => 'green',
        ];

        $statusIcons = [
            'pending' => 'fas fa-clock',
            'pending_production' => 'fas fa-hourglass-half',
            'approved' => 'fas fa-check-circle',
            'in_production' => 'fas fa-industry',
            'completed' => 'fas fa-check-double',
            'delivered' => 'fas fa-truck',
        ];

        return [
            'type' => 'production_order_status_changed',
            'title' => 'Production Order Status Updated',
            'message' => "Order #{$this->productionOrder->order_number} status changed from " . 
                        ucfirst($this->oldStatus) . " to " . ucfirst($this->newStatus),
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'total_price' => $this->productionOrder->total_price,
            ],
            'action_url' => '/sales/orders',
            'icon' => $statusIcons[$this->newStatus] ?? 'fas fa-clipboard-list',
            'color' => $statusColors[$this->newStatus] ?? 'blue',
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
            'type' => 'production_order_status_changed',
            'title' => 'Production Order Status Updated',
            'message' => "Order #{$this->productionOrder->order_number} status changed from " . 
                        ucfirst($this->oldStatus) . " to " . ucfirst($this->newStatus),
            'data' => [
                'order_id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'total_price' => $this->productionOrder->total_price,
            ],
        ];
    }
}
