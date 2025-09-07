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

class OrderReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductionOrder $productionOrder;
    public ?int $readyBy;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductionOrder $productionOrder, ?int $readyBy = null)
    {
        $this->productionOrder = $productionOrder;
        $this->readyBy = $readyBy;
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
        return [
            'order' => [
                'id' => $this->productionOrder->id,
                'order_number' => $this->productionOrder->order_number,
                'customer_name' => $this->productionOrder->customer->name,
                'status' => $this->productionOrder->status,
                'production_end_date' => $this->productionOrder->production_end_date?->format('Y-m-d'),
                'total_price' => $this->productionOrder->formatted_total_price,
            ],
            'type' => 'order_ready',
            'message' => "🚨 URGENT: Order #{$this->productionOrder->order_number} is ready for delivery! Immediate action required!",
            'urgency' => 'high',
            'priority' => 'urgent',
            'timestamp' => now()->toISOString(),
        ];
    }
}
