<?php

namespace App\Livewire\Sales;

use App\Models\Delivery;
use App\Models\ProductionOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Deliveries extends Component
{
    use WithPagination;

    // CRUD State
    public $showDeliveryModal = false;
    public $isDeliveryEdit = false;
    public $deliveryId = null;
    public $production_order_id = '';
    public $customer_id = '';
    public $product_id = '';
    public $quantity = '';
    public $batch_number = '';
    public $unit_price = '';
    public $delivery_date = '';
    public $notes = '';
    public $showDeliveryDeleteModal = false;
    public $deleteDeliveryId = null;
    public $deliverySearch = '';
    public $deliveryPerPage = 10;

    protected function rules()
    {
        return [
            'production_order_id' => 'required|exists:production_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'batch_number' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->delivery_date = now()->format('Y-m-d');
    }

    public function openDeliveryCreateModal()
    {
        $this->resetDeliveryForm();
        $this->isDeliveryEdit = false;
        $this->showDeliveryModal = true;
        $this->delivery_date = now()->format('Y-m-d');
    }

    public function openDeliveryEditModal($id)
    {
        $delivery = Delivery::findOrFail($id);
        $this->deliveryId = $delivery->id;
        $this->production_order_id = $delivery->production_order_id;
        $this->customer_id = $delivery->customer_id;
        $this->product_id = $delivery->product_id;
        $this->quantity = $delivery->quantity;
        $this->batch_number = $delivery->batch_number;
        $this->unit_price = $delivery->unit_price;
        $this->delivery_date = $delivery->delivery_date ? $delivery->delivery_date->format('Y-m-d') : '';
        $this->notes = $delivery->notes;
        $this->isDeliveryEdit = true;
        $this->showDeliveryModal = true;
    }

    public function saveDelivery()
    {
        $this->validate();
        $user = Auth::user();
        if ($this->isDeliveryEdit && $this->deliveryId) {
            $delivery = Delivery::findOrFail($this->deliveryId);
            $delivery->update([
                'production_order_id' => $this->production_order_id,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'unit_price' => $this->unit_price,
                'total_amount' => $this->quantity * $this->unit_price,
                'delivery_date' => $this->delivery_date,
                'delivered_by' => $user ? $user->id : null,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Delivery updated.');
        } else {
            Delivery::create([
                'production_order_id' => $this->production_order_id,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'unit_price' => $this->unit_price,
                'total_amount' => $this->quantity * $this->unit_price,
                'delivery_date' => $this->delivery_date,
                'delivered_by' => $user ? $user->id : null,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Delivery recorded.');
        }
        $this->showDeliveryModal = false;
        $this->resetDeliveryForm();
    }

    public function confirmDeliveryDelete($id)
    {
        $this->deleteDeliveryId = $id;
        $this->showDeliveryDeleteModal = true;
    }

    public function deleteDelivery()
    {
        $delivery = Delivery::findOrFail($this->deleteDeliveryId);
        $delivery->delete();
        $this->showDeliveryDeleteModal = false;
        $this->deleteDeliveryId = null;
        session()->flash('message', 'Delivery deleted.');
    }

    public function resetDeliveryForm()
    {
        $this->deliveryId = null;
        $this->production_order_id = '';
        $this->customer_id = '';
        $this->product_id = '';
        $this->quantity = '';
        $this->batch_number = '';
        $this->unit_price = '';
        $this->delivery_date = now()->format('Y-m-d');
        $this->notes = '';
    }

    public function render()
    {
        $deliveries = Delivery::with(['productionOrder', 'customer', 'product'])
            ->when($this->deliverySearch, function ($q) {
                $q->where('batch_number', 'like', "%{$this->deliverySearch}%");
            })
            ->latest()
            ->paginate($this->deliveryPerPage);
        $productionOrders = ProductionOrder::where('status', 'completed')->get();
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('livewire.sales.deliveries', [
            'deliveries' => $deliveries,
            'productionOrders' => $productionOrders,
            'customers' => $customers,
            'products' => $products,
        ]);
    }
}
