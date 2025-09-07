<?php

namespace App\Console\Commands;

use App\Models\ProductionOrder;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--user-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification system by creating a test order and changing its status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Testing notification system for user: {$user->name} ({$user->email})");

        // Find an existing production order
        $order = ProductionOrder::with('customer')->first();
        
        if (!$order) {
            $this->error("No production orders found. Please create an order first.");
            return 1;
        }

        $this->info("Using order: #{$order->order_number} for customer: {$order->customer->name}");

        // Test status change notification
        $oldStatus = $order->status;
        $newStatus = $oldStatus === 'pending' ? 'approved' : 'pending';
        
        $this->info("Changing status from '{$oldStatus}' to '{$newStatus}'...");
        
        // Update the order status - this should trigger the notification
        $order->update(['status' => $newStatus]);
        
        $this->info("Status updated successfully. Check the queue and mail logs for notifications.");
        
        // Check if there are any queued jobs
        $this->info("Checking queue status...");
        $this->call('queue:work', ['--once' => true]);
        
        return 0;
    }
}

