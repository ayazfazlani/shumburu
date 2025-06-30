<?php

namespace App\Livewire\Sales;

use App\Models\Delivery;
use App\Models\ProductionOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Deliveries extends Component
{
  public $production_order_id;
  public $customer_id;
  public $product_id;
  public $quantity;
  public $batch_number;
  public $unit_price;
  public $delivery_date;
  public $notes;

  protected $rules = [
    'production_order_id' => 'required|exists:production_orders,id',
    'customer_id' => 'required|exists:customers,id',
    'product_id' => 'required|exists:products,id',
    'quantity' => 'required|numeric|min:1',
    'batch_number' => 'required|string|max:255',
    'unit_price' => 'required|numeric|min:0',
    'delivery_date' => 'required|date',
    'notes' => 'nullable|string',
  ];

  public function mount()
  {
    $this->delivery_date = now()->format('Y-m-d');
  }

  public function save()
  {
    $this->validate();

    $user = Auth::user();

    if (!$user->hasRole(['admin', 'sales'])) {
      session()->flash('error', 'Unauthorized to record deliveries.');
      return;
    }

    Delivery::create([
      'production_order_id' => $this->production_order_id,
      'customer_id' => $this->customer_id,
      'product_id' => $this->product_id,
      'quantity' => $this->quantity,
      'batch_number' => $this->batch_number,
      'unit_price' => $this->unit_price,
      'total_amount' => $this->quantity * $this->unit_price,
      'delivery_date' => $this->delivery_date,
      'delivered_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Delivery recorded successfully.');

    $this->reset(['production_order_id', 'customer_id', 'product_id', 'quantity', 'batch_number', 'unit_price', 'notes']);
  }

  public function render()
  {
    $productionOrders = ProductionOrder::where('status', 'completed')->get();
    $customers = Customer::where('is_active', true)->get();
    $products = Product::where('is_active', true)->get();

    return view('livewire.sales.deliveries', [
      'productionOrders' => $productionOrders,
      'customers' => $customers,
      'products' => $products,
    ]);
  }
}
