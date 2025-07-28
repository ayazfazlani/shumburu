<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // --- Production Order CRUD State ---
    public $showOrderModal = false;
    public $isOrderEdit = false;
    public $orderId = null;
    public $order_number = '';
    public $customer_id = '';
    public $product_id = '';
    public $quantity = '';
    public $status = 'pending';
    public $requested_date = '';
    public $production_start_date = '';
    public $production_end_date = '';
    public $delivery_date = '';
    public $requested_by = '';
    public $approved_by = '';
    public $plant_manager_id = '';
    public $notes = '';
    public $showOrderDeleteModal = false;
    public $deleteOrderId = null;
    public $orderSearch = '';
    public $orderPerPage = 10;

    protected function orderRules()
    {
        return [
            'order_number' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'status' => 'required|string',
            'requested_date' => 'required|date',
            'production_start_date' => 'nullable|date',
            'production_end_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'requested_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'plant_manager_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function openOrderCreateModal()
    {
        $this->resetOrderForm();
        $this->isOrderEdit = false;
        $this->showOrderModal = true;
    }

    public function openOrderEditModal($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $this->orderId = $order->id;
        $this->order_number = $order->order_number;
        $this->customer_id = $order->customer_id;
        $this->product_id = $order->product_id;
        $this->quantity = $order->quantity;
        $this->status = $order->status;
        $this->requested_date = $order->requested_date ? $order->requested_date->format('Y-m-d') : '';
        $this->production_start_date = $order->production_start_date ? $order->production_start_date->format('Y-m-d') : '';
        $this->production_end_date = $order->production_end_date ? $order->production_end_date->format('Y-m-d') : '';
        $this->delivery_date = $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '';
        $this->requested_by = $order->requested_by;
        $this->approved_by = $order->approved_by;
        $this->plant_manager_id = $order->plant_manager_id;
        $this->notes = $order->notes;
        $this->isOrderEdit = true;
        $this->showOrderModal = true;
    }

    public function saveOrder()
    {
        $this->validate($this->orderRules());
        if ($this->isOrderEdit && $this->orderId) {
            $order = ProductionOrder::findOrFail($this->orderId);
            $order->update([
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'status' => $this->status,
                'requested_date' => $this->requested_date,
                'production_start_date' => $this->production_start_date,
                'production_end_date' => $this->production_end_date,
                'delivery_date' => $this->delivery_date,
                'requested_by' => $this->requested_by,
                'approved_by' => $this->approved_by,
                'plant_manager_id' => $this->plant_manager_id,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Production order updated.');
        } else {
            ProductionOrder::create([
                'order_number' => $this->order_number,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'status' => $this->status,
                'requested_date' => $this->requested_date,
                'production_start_date' => $this->production_start_date,
                'production_end_date' => $this->production_end_date,
                'delivery_date' => $this->delivery_date,
                'requested_by' => $this->requested_by,
                'approved_by' => $this->approved_by,
                'plant_manager_id' => $this->plant_manager_id,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Production order created.');
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
        $this->product_id = '';
        $this->quantity = '';
        $this->status = 'pending';
        $this->requested_date = '';
        $this->production_start_date = '';
        $this->production_end_date = '';
        $this->delivery_date = '';
        $this->requested_by = '';
        $this->approved_by = '';
        $this->plant_manager_id = '';
        $this->notes = '';
    }

    public function render()
    {
        $user = Auth::user();
        // if (!$user->hasRole(['Admin', 'Sales'])) {
        //     abort(403, 'Unauthorized access to sales.');
        // }
        $productionOrders = ProductionOrder::with(['customer', 'product', 'requestedBy', 'approvedBy', 'plantManager'])
            ->when($this->orderSearch, function ($q) {
                $q->where('order_number', 'like', "%{$this->orderSearch}%");
            })
            ->latest()
            ->paginate($this->orderPerPage);
        $deliveries = Delivery::with(['customer', 'product', 'productionOrder', 'deliveredBy'])
            ->latest()
            ->paginate(10);
        $payments = Payment::with(['customer', 'order', 'recordedBy'])
            ->latest()
            ->paginate(10);
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $users = User::all();
        return view('livewire.sales.index', [
            'productionOrders' => $productionOrders,
            'deliveries' => $deliveries,
            'payments' => $payments,
            'customers' => $customers,
            'products' => $products,
            'users' => $users,
        ]);
    }
}