<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CreateOrder extends Component
{
    use WithPagination;

    // CRUD State
    public $showOrderModal = false;
    public $isOrderEdit = false;
    public $orderId = null;
    public $order_number = '';
    public $customer_id = '';
    public $customerSearch = '';
    public $selectedCustomerName = '';
    public $status = 'pending';
    public $requested_date = '';
    public $requested_by = '';
    public $notes = '';
    public $showOrderDeleteModal = false;
    public $deleteOrderId = null;
    public $orderSearch = '';
    public $orderPerPage = 10;
    public $filteredCustomers = [];

    protected function rules()
    {
        return [
            'order_number' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|string',
            'requested_date' => 'required|date',
            'requested_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->filteredCustomers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function openOrderCreateModal()
    {
        $this->resetOrderForm();
        $this->isOrderEdit = false;
        $this->showOrderModal = true;
        $this->order_number = 'PO-' . date('Ymd') . '-' . str_pad(ProductionOrder::count() + 1, 4, '0', STR_PAD_LEFT);
        $this->requested_date = now()->format('Y-m-d');
    }

    public function openOrderEditModal($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $this->orderId = $order->id;
        $this->order_number = $order->order_number;
        $this->customer_id = $order->customer_id;
        $this->selectedCustomerName = $order->customer->name ?? '';
        $this->status = $order->status;
        $this->requested_date = $order->requested_date ? $order->requested_date->format('Y-m-d') : '';
        $this->requested_by = $order->requested_by;
        $this->notes = $order->notes;
        $this->isOrderEdit = true;
        $this->showOrderModal = true;
    }

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
        
        // Reset selection if search doesn't match
        if ($this->customer_id && $this->selectedCustomerName) {
            $found = false;
            foreach ($this->filteredCustomers as $customer) {
                if ($customer['id'] == $this->customer_id) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $this->customer_id = '';
                $this->selectedCustomerName = '';
            }
        }
    }

    public function selectCustomer($customerId, $customerName)
    {
        $this->customer_id = $customerId;
        $this->selectedCustomerName = $customerName;
        $this->customerSearch = ''; // Clear search after selection
        $this->updatedCustomerSearch(); // Refresh the list
    }

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
            session()->flash('message', 'Production order updated.');
        } else {
            $order = ProductionOrder::create([
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
                'requested_date' => $this->requested_date,
                'requested_by' => $user ? $user->id : null,
                'notes' => $this->notes,
            ]);
            
            // Send notifications for new order
            $notificationService = app(NotificationService::class);
            $notificationService->notifyOrderCreated($order);
            
            session()->flash('message', 'Production order created and notifications sent.');
        }
        $this->showOrderModal = false;
        $this->resetOrderForm();
    }

    public function confirmOrderDelete($id)
    {
        $this->deleteOrderId = $id;
        $this->showOrderDeleteModal = true;
    }

    public function deleteOrder()
    {
        $order = ProductionOrder::findOrFail($this->deleteOrderId);
        $order->delete();
        $this->showOrderDeleteModal = false;
        $this->deleteOrderId = null;
        session()->flash('message', 'Production order deleted.');
    }

    public function resetOrderForm()
    {
        $this->orderId = null;
        $this->order_number = '';
        $this->customer_id = '';
        $this->customerSearch = '';
        $this->selectedCustomerName = '';
        $this->status = 'pending';
        $this->requested_date = '';
        $this->requested_by = '';
        $this->notes = '';
        $this->filteredCustomers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function render()
    {
        $orders = ProductionOrder::with(['customer'])
            ->when($this->orderSearch, function ($q) {
                $q->where('order_number', 'like', "%{$this->orderSearch}%");
            })
            ->latest()
            ->paginate($this->orderPerPage);
            
        $products = Product::where('is_active', true)->get();
        
        return view('livewire.sales.create-order', [
            'orders' => $orders,
            'products' => $products,
        ]);
    }
}