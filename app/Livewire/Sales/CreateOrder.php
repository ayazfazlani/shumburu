<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class CreateOrder extends Component
{
    use WithPagination;

    // ─── Modal State ───
    public $showOrderModal = false;
    public $showOrderDeleteModal = false;
    public $isOrderEdit = false;
    public $orderId = null;
    public $deleteOrderId = null;

    // ─── Form Fields ───
    public $order_number = '';
    public $customer_id = '';
    public $customerSearch = '';
    public $selectedCustomerName = '';
    public $status = 'pending';
    public $requested_date = '';
    public $requested_by = '';
    public $notes = '';

    // ─── Filters ───
    public $orderSearch = '';
    public $orderPerPage = 10;
    public $filteredCustomers = [];

    // ─── Rules ───
    protected function rules()
    {
        return [
            'order_number' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|string|in:pending,approved,in_production,completed,delivered',
            'requested_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    // ─── Mount ───
    public function mount()
    {
        abort_unless(auth()->user()->can('sales.create-order'), 403);
        $this->loadCustomers();
        $this->requested_date = now()->format('Y-m-d');
    }

    // ─── Load Customers ───
    public function loadCustomers()
    {
        $this->filteredCustomers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    // ─── Open Create Modal ───
    public function openOrderCreateModal()
    {
        $this->resetOrderForm();
        $this->isOrderEdit = false;
        $this->showOrderModal = true;
        $this->order_number = 'PO-' . date('Ymd') . '-' . str_pad(ProductionOrder::count() + 1, 4, '0', STR_PAD_LEFT);
        $this->requested_date = now()->format('Y-m-d');
        $this->loadCustomers();
    }

    // ─── Open Edit Modal ───
    public function openOrderEditModal($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $this->orderId = $order->id;
        $this->order_number = $order->order_number;
        $this->customer_id = $order->customer_id;
        $this->selectedCustomerName = $order->customer->name ?? '';
        $this->status = $order->status;
        $this->requested_date = $order->requested_date ? $order->requested_date->format('Y-m-d') : '';
        $this->notes = $order->notes;
        $this->isOrderEdit = true;
        $this->showOrderModal = true;
        $this->loadCustomers();
    }

    // ─── Customer Search ───
    public function updatedCustomerSearch()
    {
        if (empty($this->customerSearch)) {
            $this->filteredCustomers = Customer::where('is_active', true)
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            $this->filteredCustomers = Customer::where('is_active', true)
                ->where('name', 'like', '%' . $this->customerSearch . '%')
                ->orderBy('name')
                ->get()
                ->toArray();
        }
    }

    // ─── Select Customer ───
    public function selectCustomer($customerId, $customerName)
    {
        $this->customer_id = $customerId;
        $this->selectedCustomerName = $customerName;
        $this->customerSearch = '';
        $this->filteredCustomers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    // ─── Save Order ───
    public function saveOrder()
    {
        $this->validate();
        $user = Auth::user();

        if ($this->isOrderEdit && $this->orderId) {
            $order = ProductionOrder::findOrFail($this->orderId);
            $order->update([
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
                'requested_date' => $this->requested_date,
                'requested_by' => $user ? $user->id : null,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Production order updated successfully.');
        } else {
            $order = ProductionOrder::create([
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
                'requested_date' => $this->requested_date,
                'requested_by' => $user ? $user->id : null,
                'notes' => $this->notes,
            ]);

            try {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyOrderCreated($order);
                session()->flash('message', 'Production order created and notifications sent.');
            } catch (\Exception $e) {
                session()->flash('message', 'Production order created successfully.');
            }
        }

        $this->showOrderModal = false;
        $this->resetOrderForm();
    }

    // ─── Confirm Delete ───
    public function confirmOrderDelete($id)
    {
        $this->deleteOrderId = $id;
        $this->showOrderDeleteModal = true;
    }

    // ─── Delete Order ───
    public function deleteOrder()
    {
        $order = ProductionOrder::findOrFail($this->deleteOrderId);
        $order->delete();
        $this->showOrderDeleteModal = false;
        $this->deleteOrderId = null;
        session()->flash('message', 'Production order deleted successfully.');
    }

    // ─── Reset Form ───
    public function resetOrderForm()
    {
        $this->orderId = null;
        $this->order_number = '';
        $this->customer_id = '';
        $this->customerSearch = '';
        $this->selectedCustomerName = '';
        $this->status = 'pending';
        $this->requested_date = '';
        $this->notes = '';
        $this->loadCustomers();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // ─── Render ───
    public function render()
    {
        $orders = ProductionOrder::with(['customer'])
            ->when($this->orderSearch, function ($q) {
                $q->where('order_number', 'like', "%{$this->orderSearch}%");
            })
            ->latest()
            ->paginate($this->orderPerPage);

        return view('livewire.sales.create-order', [
            'orders' => $orders,
        ]);
    }
}
