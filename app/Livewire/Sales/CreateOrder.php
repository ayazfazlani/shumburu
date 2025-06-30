<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateOrder extends Component
{
  public $customer_id;
  public $product_id;
  public $quantity;
  public $notes;

  protected $rules = [
    'customer_id' => 'required|exists:customers,id',
    'product_id' => 'required|exists:products,id',
    'quantity' => 'required|numeric|min:1',
    'notes' => 'nullable|string',
  ];

  public function save()
  {
    $this->validate();

    $user = Auth::user();

    if (!$user->hasRole(['admin', 'sales'])) {
      session()->flash('error', 'Unauthorized to create orders.');
      return;
    }

    $orderNumber = 'PO-' . date('Ymd') . '-' . str_pad(ProductionOrder::count() + 1, 4, '0', STR_PAD_LEFT);

    ProductionOrder::create([
      'order_number' => $orderNumber,
      'customer_id' => $this->customer_id,
      'product_id' => $this->product_id,
      'quantity' => $this->quantity,
      'status' => 'pending',
      'requested_date' => now(),
      'requested_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Production order created successfully.');

    $this->reset(['customer_id', 'product_id', 'quantity', 'notes']);
  }

  public function render()
  {
    $customers = Customer::where('is_active', true)->get();
    $products = Product::where('is_active', true)->get();

    return view('livewire.sales.create-order', [
      'customers' => $customers,
      'products' => $products,
    ]);
  }
}
