<?php

namespace App\Events;

use App\Models\ProductionOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionOrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductionOrder $productionOrder;
    public string $oldStatus;
    public string $newStatus;
    public ?int $changedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductionOrder $productionOrder, string $oldStatus, string $newStatus, ?int $changedBy = null)
    {
        $this->productionOrder = $productionOrder;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('production-orders'),
            new PrivateChannel('notifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $statusMessages = [
            'pending' => 'Order is pending approval',
            'approved' => 'Order has been approved and ready for production',
            'in_production' => 'Production has started',
            'completed' => 'Production has been completed',
            'delivered' => 'Order has been delivered to customer',
        ];

        return [
            'order' => [
                'id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'requested_date' => $this->productionOrder->requested_date->format('Y-m-d'),
                'total_price' => $this->productionOrder->formatted_total_price,
            ],
            'type' => 'production_order_status_changed',
            'message' => "Production order #{$this->productionOrder->order_number} status changed from " . 
                        ucfirst($this->oldStatus) . " to " . ucfirst($this->newStatus),
            'status_message' => $statusMessages[$this->newStatus] ?? 'Status updated',
            'timestamp' => now()->toISOString(),
        ];
    }
}
