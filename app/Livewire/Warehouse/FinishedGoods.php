<?php

namespace App\Livewire\Warehouse;

use App\Models\FinishedGood;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use phpDocumentor\Reflection\Types\This;
use App\Models\MaterialStockOut;
use App\Models\ProductionLine;
use App\Models\MaterialStockOutLine;
use Livewire\WithPagination;

class FinishedGoods extends Component
{
  use WithPagination;
  public $product_id;
  public $type = 'roll';
  public $length_m;
  public $quantity;
  public $waste_quantity = 0;
  public $batch_number;
  public $production_date;
  public $purpose = 'for_stock';
  public $customer_id;
  public $notes;
  public $finishedGoods;

  // remaining fields 

  public $size;
  public $totalWeight;
  public $outerDiameter;
  public $Surface;
  public $thickness;
  public $startOvality;
  public $endOvality;
  public $stripeColor;

  // Removed: material_stock_out_id, production_line_id, quantity_used

  protected $rules = [
    'product_id' => 'required|exists:products,id',
    'type' => 'required|in:roll,cut',
    'length_m' => 'required|numeric|min:0.01',
    'quantity' => 'required|numeric|min:0.01',
    'waste_quantity' => 'nullable|numeric|min:0',
    'batch_number' => 'required|string|max:255',
    'production_date' => 'required|date',
    'purpose' => 'required|in:for_stock,for_customer_order',
    'customer_id' => 'nullable|required_if:purpose,for_customer_order|exists:customers,id',
    'notes' => 'nullable|string',
    'size' => 'nullable|numeric',
    'outerDiameter' => 'nullable|numeric',
    'Surface' => 'nullable|string',
    'thickness' => 'nullable|string',
    'startOvality' => 'nullable|string',
    'endOvality' => 'nullable|string',
    'stripeColor' => 'nullable|string'
  ];

  public function mount()
  {
    $this->type = 'roll';
    $this->production_date = now()->format('Y-m-d');
    $this->finishedGoods = FinishedGood::with([
      'product'
      ])->get();
  }

  public function updatedPurpose()
  {
    if ($this->purpose === 'for_stock') {
      $this->customer_id = null;
    }
  }

  public function save()
  {
    $this->validate();
    $user = Auth::user();
    if($this->product_id){
    $weightPerMeter = Product::where('id', $this->product_id)->pluck('weight_per_meter');
    $totalweight = $weightPerMeter[0] * $this->quantity;
    // dd($total_weight);
    // return;
    }
  
    FinishedGood::create([
      'product_id' => $this->product_id,
      'type' => $this->type,
      'length_m' => $this->length_m,
      'quantity' => $this->quantity,
      'waste_quantity' => $this->waste_quantity ?? 0,
      'batch_number' => $this->batch_number,
      'production_date' => $this->production_date,
      'purpose' => $this->purpose,
      'customer_id' => $this->customer_id,
      'produced_by' => $user->id,
      'notes' => $this->notes,
      'size' => $this->size,
      'total_weight'=> $totalweight,
      'outer_diameter'=> $this->outerDiameter,
      'surface' => $this->Surface,
      'thickness' => $this->thickness,
      'start_ovality' => $this->startOvality,
      'end_ovality' => $this->endOvality,
      'stripe_color' => $this->stripeColor
    ]);
    session()->flash('message', 'Finished goods recorded successfully.');
    $this->reset(['product_id', 'type', 'length_m', 'quantity', 'waste_quantity', 'batch_number', 'customer_id', 'notes']);
    $this->production_date = now()->format('Y-m-d');
    $this->finishedGoods = FinishedGood::with('product')->get();
  }

  public function render()
  {
    $products = Product::where('is_active', true)->get();
    $customers = Customer::where('is_active', true)->get();
    return view('livewire.warehouse.finished-goods', [
      'products' => $products,
      'customers' => $customers,
    ]);
  }
}