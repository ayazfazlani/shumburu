<?php

namespace App\Console\Commands;

use App\Models\ProductionOrder;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestCompleteNotificationFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-flow {--order-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test complete notification flow for all status changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->option('order-id');
        
        if ($orderId) {
            $order = ProductionOrder::with('customer')->find($orderId);
        } else {
            $order = ProductionOrder::with('customer')->first();
        }
        
        if (!$order) {
            $this->error("No production orders found. Please create an order first.");
            return 1;
        }

        $this->info("Testing complete notification flow for order: #{$order->order_number}");
        $this->info("Customer: {$order->customer->name}");
        $this->newLine();

        // Define the complete status flow
        $statusFlow = [
            'pending' => 'Order created (pending approval)',
            'approved' => 'Order approved (ready for production)',
            'in_production' => '🚨 PRODUCTION STARTED 🚨 (CRITICAL)',
            'completed' => '🎉 PRODUCTION COMPLETED 🎉 (CRITICAL)',
            'delivered' => 'Order delivered to customer'
        ];

        $originalStatus = $order->status;
        $this->info("Original status: {$originalStatus}");
        $this->newLine();

        foreach ($statusFlow as $status => $description) {
            if ($status === $originalStatus) {
                $this->info("Skipping {$status} - already current status");
                continue;
            }

            $this->info("Testing status change to: {$status}");
            $this->info("Description: {$description}");
            
            // Update the order status
            $oldStatus = $order->status;
            $order->update(['status' => $status]);
            
            $this->info("✅ Status updated from '{$oldStatus}' to '{$status}'");
            
            // Process the queue to send notifications
            $this->call('queue:work', ['--once' => true]);
            
            $this->info("📧 Notifications sent for status change");
            $this->newLine();
            
            // Small delay to see the process
            usleep(500000); // 0.5 seconds
        }

        // Restore original status
        $order->update(['status' => $originalStatus]);
        $this->info("🔄 Restored original status: {$originalStatus}");
        
        $this->newLine();
        $this->info("✅ Complete notification flow test completed!");
        $this->info("Check your email and notification center for all status change notifications.");

        return 0;
    }
}
