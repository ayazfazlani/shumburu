<?php

namespace App\Livewire\Operations;

use App\Models\ProductionOrder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductionOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function render()
    {
        $orders = ProductionOrder::with(['customer', 'items.product'])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                })->orWhere('order_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.operations.production-orders', [
            'orders' => $orders,
        ]);
    }

    public function markAsCompleted($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $oldStatus = $order->status;
        
        // Update status to completed
        $order->update([
            'status' => 'completed',
            'production_end_date' => now()->toDateString(),
        ]);
        
        // Explicitly send notifications as backup (in case model observer doesn't fire)
        $notificationService = app(NotificationService::class);
        $notificationService->notifyStatusChanged($order, $oldStatus, 'completed', auth()->id());
        
        session()->flash('message', "Order #{$order->order_number} marked as completed and notifications sent to sales team!");
    }

    public function markAsInProduction($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $oldStatus = $order->status;
        
        // Update status to in_production
        $order->update([
            'status' => 'in_production',
            'production_start_date' => now()->toDateString(),
        ]);
        
        // Explicitly send notifications as backup (in case model observer doesn't fire)
        $notificationService = app(NotificationService::class);
        $notificationService->notifyStatusChanged($order, $oldStatus, 'in_production', auth()->id());
        
        session()->flash('message', "Order #{$order->order_number} marked as in production and notifications sent!");
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'pending_production' => 'badge-info',
            'approved' => 'badge-primary',
            'in_production' => 'badge-secondary',
            'completed' => 'badge-success',
            'delivered' => 'badge-success',
            default => 'badge-neutral'
        };
    }

    public function getStatusIcon($status)
    {
        return match($status) {
            'pending' => 'fas fa-clock',
            'pending_production' => 'fas fa-hourglass-half',
            'approved' => 'fas fa-check-circle',
            'in_production' => 'fas fa-industry',
            'completed' => 'fas fa-check-double',
            'delivered' => 'fas fa-truck',
            default => 'fas fa-clipboard-list'
        };
    }
}
