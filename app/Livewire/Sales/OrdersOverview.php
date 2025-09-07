<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersOverview extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $customerFilter = '';
    public $dateFilter = '';
    
    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal states
    public $showOrderDetailsModal = false;
    public $selectedOrder = null;
    public $showPaymentModal = false;
    public $showDeliveryModal = false;

    // Payment form
    public $paymentAmount;
    public $paymentMethod = 'cash';
    public $paymentDate;
    public $paymentNotes;

    // Delivery form
    public $deliveryQuantity;
    public $deliveryDate;
    public $deliveryNotes;

    protected $rules = [
        'paymentAmount' => 'required|numeric|min:0.01',
        'paymentMethod' => 'required|string',
        'paymentDate' => 'required|date',
        'deliveryQuantity' => 'required|numeric|min:0.01',
        'deliveryDate' => 'required|date',
    ];

    public function mount(): void
    {
        $this->paymentDate = now()->format('Y-m-d');
        $this->deliveryDate = now()->format('Y-m-d');
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $orders = ProductionOrder::with(['customer', 'items.product', 'payments',
        //  'deliveries'
         ])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                })->orWhere('order_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->customerFilter, function ($query) {
                $query->where('customer_id', $this->customerFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('requested_date', $this->dateFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $customers = \App\Models\Customer::where('is_active', true)->orderBy('name')->get();

        return view('livewire.sales.orders-overview', [
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = ProductionOrder::with(['customer', 'items.product', 'payments', 
            // 'deliveries'
        ])
            ->findOrFail($orderId);
        $this->showOrderDetailsModal = true;
    }

    public function addPayment($orderId)
    {
        $this->selectedOrder = ProductionOrder::findOrFail($orderId);
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|string',
            'paymentDate' => 'required|date',
        ]);

        Payment::create([
            'production_order_id' => $this->selectedOrder->id,
            'customer_id' => $this->selectedOrder->customer_id,
            'amount' => $this->paymentAmount,
            'payment_method' => $this->paymentMethod,
            'payment_date' => $this->paymentDate,
            'notes' => $this->paymentNotes,
            'recorded_by' => Auth::id(),
        ]);

        session()->flash('message', 'Payment recorded successfully.');
        $this->closePaymentModal();
    }

    public function addDelivery($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $oldStatus = $order->status;
        
        // Update status to delivered
        $order->update(['status' => 'delivered']);
        
        // Explicitly send notifications as backup (in case model observer doesn't fire)
        $notificationService = app(NotificationService::class);
        $notificationService->notifyStatusChanged($order, $oldStatus, 'delivered', Auth::id());
        
        session()->flash('message', 'Order marked as delivered and notifications sent to sales team!');
        $this->mount();
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryQuantity' => 'required|numeric|min:0.01',
            'deliveryDate' => 'required|date',
        ]);

        // For simplicity, we'll create delivery for the first item
        $firstItem = $this->selectedOrder->items->first();
        
        Delivery::create([
            'production_order_id' => $this->selectedOrder->id,
            'customer_id' => $this->selectedOrder->customer_id,
            'product_id' => $firstItem->product_id,
            'quantity' => $this->deliveryQuantity,
            'batch_number' => 'BATCH-' . time(),
            'unit_price' => $firstItem->unit_price,
            'total_amount' => $this->deliveryQuantity * $firstItem->unit_price,
            'delivery_date' => $this->deliveryDate,
            'delivered_by' => Auth::id(),
            'notes' => $this->deliveryNotes,
        ]);

        // Update order status if all items delivered
        $totalOrdered = $this->selectedOrder->items->sum('quantity');
        $totalDelivered = $this->selectedOrder->deliveries->sum('quantity') + $this->deliveryQuantity;
        
        if ($totalDelivered >= $totalOrdered) {
            $oldStatus = $this->selectedOrder->status;
            // Update status to delivered
            $this->selectedOrder->update(['status' => 'delivered']);
            
            // Explicitly send notifications as backup (in case model observer doesn't fire)
            $notificationService = app(NotificationService::class);
            $notificationService->notifyStatusChanged($this->selectedOrder, $oldStatus, 'delivered', Auth::id());
        }

        session()->flash('message', 'Delivery recorded successfully.');
        $this->closeDeliveryModal();
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $oldStatus = $order->status;
        
        // Update status
        $order->update(['status' => $status]);
        
        // Explicitly send notifications as backup (in case model observer doesn't fire)
        $notificationService = app(NotificationService::class);
        $notificationService->notifyStatusChanged($order, $oldStatus, $status, Auth::id());
        
        session()->flash('message', "Order status updated from {$oldStatus} to {$status}. Notifications sent!");
    }

    public function closeOrderDetailsModal()
    {
        $this->showOrderDetailsModal = false;
        $this->selectedOrder = null;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedOrder = null;
        $this->paymentAmount = '';
        $this->paymentMethod = 'cash';
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentNotes = '';
    }

    public function closeDeliveryModal()
    {
        $this->showDeliveryModal = false;
        $this->selectedOrder = null;
        $this->deliveryQuantity = '';
        $this->deliveryDate = now()->format('Y-m-d');
        $this->deliveryNotes = '';
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

    public function getOrderTotal($order)
    {
        return $order->items->sum('total_price');
    }

    public function getTotalPaid($order)
    {
        return $order->payments->sum('amount');
    }

    public function getPaymentProgress($order)
    {
        $total = $this->getOrderTotal($order);
        if ($total == 0) return 0;
        return ($this->getTotalPaid($order) / $total) * 100;
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
} 