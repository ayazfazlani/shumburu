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

class FinishedGoods extends Component
{
  public $product_id;
  public $quantity;
  public $batch_number;
  public $production_date;
  public $purpose = 'for_stock';
  public $customer_id;
  public $notes;
  public $material_stock_out_id;
  public $production_line_id;
  public $quantity_used;

  public $finishedGoods;

  protected $rules = [
    'product_id' => 'required|exists:products,id',
    'quantity' => 'required|numeric|min:0.01',
    'batch_number' => 'required|string|max:255',
    'production_date' => 'required|date',
    'purpose' => 'required|in:for_stock,for_customer_order',
    'customer_id' => 'nullable|required_if:purpose,for_customer_order|exists:customers,id',
    'notes' => 'nullable|string',
    'material_stock_out_id' => 'required|exists:material_stock_outs,id',
    'production_line_id' => 'required|exists:production_lines,id',
    'quantity_used' => 'required|numeric|min:0.001',
  ];

  public function mount()
  {
    $this->production_date = now()->format('Y-m-d');
    $this->finishedGoods = FinishedGood::with('product')->get();
    // dd($this->finishedGoods);
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

    // $stockOutLine = MaterialStockOutLine::Create([
    //   'material_stock_out_id' => $this->material_stock_out_id,
    //   'production_line_id' => $this->production_line_id,
    // ], [
    //   'quantity_consumed' => $this->quantity_used,
    // ]);

    $stockOutLine = \App\Models\MaterialStockOutLine::create([
      'material_stock_out_id' => $this->material_stock_out_id,
      'production_line_id' => $this->production_line_id,
      'quantity_consumed' => $this->quantity_used,
    ]);

    FinishedGood::create([
      'product_id' => $this->product_id,
      'material_stock_out_line_id' => $stockOutLine->id,
      'quantity' => $this->quantity,
      'batch_number' => $this->batch_number,
      'production_date' => $this->production_date,
      'purpose' => $this->purpose,
      'customer_id' => $this->customer_id,
      'produced_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Finished goods recorded successfully.');

    $this->reset(['product_id', 'quantity', 'batch_number', 'customer_id', 'notes', 'material_stock_out_id', 'production_line_id', 'quantity_used']);
  }

  public function render()
  {
    $products = Product::where('is_active', true)->get();
    $customers = Customer::where('is_active', true)->get();
    $stockOuts = MaterialStockOut::all();
    $lines = ProductionLine::all();

    return view('livewire.warehouse.finished-goods', [
      'products' => $products,
      'customers' => $customers,
      'stockOuts' => $stockOuts,
      'lines' => $lines,
    ]);
  }
}