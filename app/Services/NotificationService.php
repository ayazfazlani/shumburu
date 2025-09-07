<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProductionOrder;
use App\Models\Department;
use App\Events\ProductionOrderCreated;
use App\Events\ProductionOrderStatusChanged;
use App\Events\ProductionStarted;
use App\Events\ProductionCompleted;
use App\Events\OrderReady;
use App\Events\OrderDelivered;
use App\Notifications\ProductionOrderCreatedNotification;
use App\Notifications\ProductionOrderStatusChangedNotification;
use App\Notifications\ProductionStartedNotification;
use App\Notifications\ProductionCompletedNotification;
use App\Notifications\OrderReadyNotification;
use App\Notifications\OrderDeliveredNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notifications when a production order is created
     */
    public function notifyOrderCreated(ProductionOrder $productionOrder): void
    {
        // Dispatch the event for real-time updates
        event(new ProductionOrderCreated($productionOrder));

        // Get users to notify based on roles and departments
        $usersToNotify = $this->getUsersForOrderCreated($productionOrder);

        // Send notifications
        foreach ($usersToNotify as $user) {
            $user->notifyNow(new ProductionOrderCreatedNotification($productionOrder), ['mail', 'database']);
        }
    }

    /**
     * Send notifications when production order status changes
     */
    public function notifyStatusChanged(ProductionOrder $productionOrder, string $oldStatus, string $newStatus, ?int $changedBy = null): void
    {
        \Log::info("NotificationService::notifyStatusChanged called for order #{$productionOrder->order_number} from '{$oldStatus}' to '{$newStatus}'");
        
        // Dispatch the event for real-time updates
        event(new ProductionOrderStatusChanged($productionOrder, $oldStatus, $newStatus, $changedBy));

        // Get users to notify based on the new status
        $usersToNotify = $this->getUsersForStatusChange($productionOrder, $newStatus);
        
        \Log::info("Found " . $usersToNotify->count() . " users to notify for status change to '{$newStatus}'");

        // Send notifications for EVERY status change
        foreach ($usersToNotify as $user) {
            \Log::info("Sending notification to user: {$user->name} ({$user->email})");
            $user->notifyNow(new ProductionOrderStatusChangedNotification($productionOrder, $oldStatus, $newStatus), ['mail', 'database']);
        }

        // Send additional specific notifications for critical status changes
        if ($newStatus === 'in_production') {
            $this->notifyProductionStarted($productionOrder, $changedBy);
        } elseif ($newStatus === 'completed') {
            $this->notifyOrderReady($productionOrder, $changedBy);
        } elseif ($newStatus === 'delivered') {
            $this->notifyOrderDelivered($productionOrder, $changedBy);
        }
    }

    /**
     * Send notifications when production starts
     */
    public function notifyProductionStarted(ProductionOrder $productionOrder, ?int $startedBy = null): void
    {
        // Dispatch the event for real-time updates
        event(new ProductionStarted($productionOrder, $startedBy));

        // Get users to notify for production started
        $usersToNotify = $this->getUsersForProductionStarted($productionOrder);

        // Send notifications
        foreach ($usersToNotify as $user) {
            $user->notifyNow(new ProductionStartedNotification($productionOrder), ['mail', 'database']);
        }
    }

    /**
     * Send notifications when order is ready (completed)
     */
    public function notifyOrderReady(ProductionOrder $productionOrder, ?int $readyBy = null): void
    {
        // Dispatch the event for real-time updates
        event(new OrderReady($productionOrder, $readyBy));

        // Get users to notify for order ready
        $usersToNotify = $this->getUsersForOrderReady($productionOrder);

        // Send notifications
        foreach ($usersToNotify as $user) {
            $user->notifyNow(new OrderReadyNotification($productionOrder), ['mail', 'database']);
        }
    }

    /**
     * Send notifications when order is delivered
     */
    public function notifyOrderDelivered(ProductionOrder $productionOrder, ?int $deliveredBy = null): void
    {
        // Dispatch the event for real-time updates
        event(new OrderDelivered($productionOrder, $deliveredBy));

        // Get users to notify for order delivered
        $usersToNotify = $this->getUsersForOrderDelivered($productionOrder);

        // Send notifications
        foreach ($usersToNotify as $user) {
            $user->notifyNow(new OrderDeliveredNotification($productionOrder), ['mail', 'database']);
        }
    }

    /**
     * Get users to notify when an order is created
     */
    public function getUsersForOrderCreated(ProductionOrder $productionOrder): \Illuminate\Support\Collection
    {
        $users = collect();

        // Always notify the plant manager if assigned
        if ($productionOrder->plant_manager_id) {
            $users->push($productionOrder->plantManager);
        }

        // Notify Operations department (production managers)
        $operationsUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Operations');
        })->get();
        $users = $users->merge($operationsUsers);

        // Notify users with specific permissions
        $permissionUsers = User::permission('access operations')->get();
        $users = $users->merge($permissionUsers);

        // Notify admin users
        $adminUsers = User::role('Super Admin')->get();
        $users = $users->merge($adminUsers);

        // Remove duplicates and the user who created the order
        return $users->unique('id')->reject(function ($user) use ($productionOrder) {
            return $user->id === $productionOrder->requested_by;
        })->values();
    }

    /**
     * Get users to notify based on status change
     */
    public function getUsersForStatusChange(ProductionOrder $productionOrder, string $newStatus): \Illuminate\Support\Collection
    {
        $users = collect();

        switch ($newStatus) {
            case 'approved':
                // Notify Sales team and the user who requested the order
                $salesUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Sales');
                })->get();
                $users = $users->merge($salesUsers);

                if ($productionOrder->requested_by) {
                    $users->push($productionOrder->requestedBy);
                }
                break;

            case 'pending_production':
            case 'in_production':
                // Notify Sales team and Operations team
                $salesUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Sales');
                })->get();
                $users = $users->merge($salesUsers);

                $operationsUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Operations');
                })->get();
                $users = $users->merge($operationsUsers);
                break;

            case 'completed':
            case 'ready':
                // Notify Sales team, Admin, and Customer (if they have an account)
                $salesUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Sales');
                })->get();
                $users = $users->merge($salesUsers);

                 $adminUsers = User::role(['Super Admin','Admin'])->get();
                $users = $users->merge($adminUsers);

                // TODO: Add customer notification if they have user accounts
                break;

            case 'delivered':
                // Notify all stakeholders
                $allRelevantUsers = User::whereHas('department', function ($query) {
                    $query->whereIn('name', ['Sales', 'Operations', 'Finance', 'Administration']);
                })->get();
                $users = $users->merge($allRelevantUsers);

                 $adminUsers = User::role(['Super Admin','Admin'])->get();
                $users = $users->merge($adminUsers);
                break;

            default:
                // For any other status changes, notify relevant stakeholders
                $salesUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Sales');
                })->get();
                $users = $users->merge($salesUsers);

                $operationsUsers = User::whereHas('department', function ($query) {
                    $query->where('name', 'Operations');
                })->get();
                $users = $users->merge($operationsUsers);

                $adminUsers = User::role(['Super Admin','Admin'])->get();
                $users = $users->merge($adminUsers);
                break;
        }

        // Always notify the plant manager if assigned
        if ($productionOrder->plant_manager_id) {
            $users->push($productionOrder->plantManager);
        }

        // Remove duplicates
        return $users->unique('id')->values();
    }

    /**
     * Get users to notify when production starts
     */
    public function getUsersForProductionStarted(ProductionOrder $productionOrder): \Illuminate\Support\Collection
    {
        $users = collect();

        // Notify Sales team - they need to track production progress
        $salesUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Sales');
        })->get();
        $users = $users->merge($salesUsers);

        // Notify Operations team - they're managing production
        $operationsUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Operations');
        })->get();
        $users = $users->merge($operationsUsers);

        // Notify the user who requested the order
        if ($productionOrder->requested_by) {
            $users->push($productionOrder->requestedBy);
        }

        // Notify admin users
        $adminUsers = User::role(['Super Admin','Admin'])->get();
        $users = $users->merge($adminUsers);

        // Always notify the plant manager if assigned
        if ($productionOrder->plant_manager_id) {
            $users->push($productionOrder->plantManager);
        }

        // Remove duplicates
        return $users->unique('id')->values();
    }

    /**
     * Get users to notify when order is ready (completed)
     */
    public function getUsersForOrderReady(ProductionOrder $productionOrder): \Illuminate\Support\Collection
    {
        $users = collect();

        // Notify Sales team - they need to coordinate delivery
        $salesUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Sales');
        })->get();
        $users = $users->merge($salesUsers);

        // Notify the user who requested the order
        if ($productionOrder->requested_by) {
            $users->push($productionOrder->requestedBy);
        }

        // Notify admin users
        $adminUsers = User::role(['Super Admin','Admin'])->get();
        $users = $users->merge($adminUsers);

        // Always notify the plant manager if assigned
        if ($productionOrder->plant_manager_id) {
            $users->push($productionOrder->plantManager);
        }

        // Remove duplicates
        return $users->unique('id')->values();
    }

    /**
     * Get users to notify when order is delivered
     */
    public function getUsersForOrderDelivered(ProductionOrder $productionOrder): \Illuminate\Support\Collection
    {
        $users = collect();

        // Notify Sales team - they need to follow up with customer
        $salesUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Sales');
        })->get();
        $users = $users->merge($salesUsers);

        // Notify the user who requested the order
        if ($productionOrder->requested_by) {
            $users->push($productionOrder->requestedBy);
        }

        // Notify Finance team for payment follow-up
        $financeUsers = User::whereHas('department', function ($query) {
            $query->where('name', 'Finance');
        })->get();
        $users = $users->merge($financeUsers);

        // Notify admin users
        $adminUsers = User::role(['Super Admin','Admin'])->get();
        $users = $users->merge($adminUsers);

        // Always notify the plant manager if assigned
        if ($productionOrder->plant_manager_id) {
            $users->push($productionOrder->plantManager);
        }

        // Remove duplicates
        return $users->unique('id')->values();
    }

    /**
     * Get notification preferences for a user
     */
    public function getUserNotificationPreferences(User $user): array
    {
        // This could be extended to use a user preferences table
        return [
            'email' => true,
            'database' => true,
            'real_time' => true,
        ];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
    }
}
