<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\FgStock;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public $paymentAmount = '';
    public $paymentMethod = 'cash';
    public $paymentDate = '';
    public $paymentNotes = '';

    // Delivery form
    public $deliveryQuantity = '';
    public $deliveryDate = '';
    public $deliveryNotes = '';

    protected $rules = [
        'paymentAmount' => 'required|numeric|min:0.01',
        'paymentMethod' => 'required|string',
        'paymentDate' => 'required|date',
        'deliveryQuantity' => 'required|numeric|min:0.01',
        'deliveryDate' => 'required|date',
    ];

    protected $listeners = [
        'refreshOrders' => '$refresh'
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()->can('sales.orders-overview'), 403);
        $this->paymentDate = now()->format('Y-m-d');
        $this->deliveryDate = now()->format('Y-m-d');
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $orders = ProductionOrder::with(['customer', 'items.product', 'payments'])
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
        $this->selectedOrder = ProductionOrder::with(['customer', 'items.product', 'payments'])
            ->findOrFail($orderId);
        $this->showOrderDetailsModal = true;
    }

    public function addPayment($orderId)
    {
        $this->selectedOrder = ProductionOrder::with(['customer', 'items.product', 'payments'])->findOrFail($orderId);
        $this->paymentAmount = '';
        $this->paymentMethod = 'cash';
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentNotes = '';
        $this->resetErrorBag();
        $this->showPaymentModal = true;
    }

    public function addDelivery($orderId)
    {
        $this->selectedOrder = ProductionOrder::findOrFail($orderId);
        $this->deliveryQuantity = '';
        $this->deliveryDate = now()->format('Y-m-d');
        $this->deliveryNotes = '';
        $this->resetErrorBag();
        $this->showDeliveryModal = true;
    }

    public function savePayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|string',
            'paymentDate' => 'required|date',
        ]);

        try {
            DB::transaction(function () {
                Payment::create([
                    'production_order_id' => $this->selectedOrder->id,
                    'customer_id' => $this->selectedOrder->customer_id,
                    'amount' => $this->paymentAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_date' => $this->paymentDate,
                    'notes' => $this->paymentNotes,
                    'recorded_by' => Auth::id(),
                ]);
            });

            session()->flash('message', 'Payment recorded successfully.');
            $this->closePaymentModal();
            $this->dispatch('refreshOrders');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function saveDelivery()
    {
        $this->validate([
            'deliveryQuantity' => 'required|numeric|min:0.01',
            'deliveryDate' => 'required|date',
        ]);

        $firstItem = $this->selectedOrder->items->first();

        if (!$firstItem) {
            session()->flash('error', 'No items found in this order.');
            return;
        }

        try {
            DB::transaction(function () use ($firstItem) {
                // Create delivery record
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

                $remainingToDispatch = $this->deliveryQuantity;

                // 1. Try to consume active reservations (QC Passed)
                $reservations = $firstItem->reservations()
                    ->where('status', 'active')
                    ->whereHas('fgStock', function($q) {
                        $q->where('is_qc_passed', true);
                    })
                    ->get();

                foreach ($reservations as $res) {
                    if ($remainingToDispatch <= 0) break;

                    $consumeAmount = min($remainingToDispatch, $res->quantity);

                    $batch = $res->fgStock;
                    $batch->decrement('quantity', $consumeAmount);

                    if ($consumeAmount < $res->quantity) {
                        $res->decrement('quantity', $consumeAmount);
                    } else {
                        $res->update(['status' => 'consumed']);
                    }

                    $remainingToDispatch -= $consumeAmount;
                }

                // 2. If remaining, take from QC-passed stock
                if ($remainingToDispatch > 0) {
                    $stocks = FgStock::where('product_id', $firstItem->product_id)
                        ->where('quantity', '>', 0)
                        ->where('is_qc_passed', true)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($stocks as $stock) {
                        if ($remainingToDispatch <= 0) break;

                        $deduct = min($remainingToDispatch, $stock->quantity);
                        if ($deduct > 0) {
                            $stock->decrement('quantity', $deduct);
                            $remainingToDispatch -= $deduct;
                        }
                    }
                }

                if ($remainingToDispatch > 0) {
                    throw new \Exception("Cannot dispatch: " . number_format($remainingToDispatch, 2) . " units have not passed Quality Control.");
                }

                // Update order status
                $totalDelivered = $this->selectedOrder->deliveries->sum('quantity') + $this->deliveryQuantity;
                $totalOrdered = $this->selectedOrder->items->sum('quantity');

                if ($totalDelivered >= $totalOrdered) {
                    $this->selectedOrder->update(['status' => 'delivered']);
                }
            });

            session()->flash('message', 'Delivery recorded successfully.');
            $this->closeDeliveryModal();
            $this->dispatch('refreshOrders');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateOrderStatus($orderId, $status)
    {
        try {
            $order = ProductionOrder::findOrFail($orderId);
            $oldStatus = $order->status;

            $order->update(['status' => $status]);

            session()->flash('message', "Order status updated from {$oldStatus} to {$status}.");
            $this->dispatch('refreshOrders');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update order status.');
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->customerFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
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
        $this->resetErrorBag();
    }

    public function closeDeliveryModal()
    {
        $this->showDeliveryModal = false;
        $this->selectedOrder = null;
        $this->deliveryQuantity = '';
        $this->deliveryDate = now()->format('Y-m-d');
        $this->deliveryNotes = '';
        $this->resetErrorBag();
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

    // Helper methods for views
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
            'pending' => 'bx-badge-warning',
            'pending_production' => 'bx-badge-warning',
            'approved' => 'bx-badge-info',
            'in_production' => 'bx-badge-primary',
            'completed' => 'bx-badge-success',
            'delivered' => 'bx-badge-secondary',
            default => 'bx-badge-gray'
        };
    }
}
