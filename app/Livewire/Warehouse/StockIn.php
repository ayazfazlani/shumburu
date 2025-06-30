<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockIn;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StockIn extends Component
{
  public $raw_material_id;
  public $quantity;
  public $batch_number;
  public $received_date;
  public $notes;
  public $stockIns;
  protected $rules = [
    'raw_material_id' => 'required|exists:raw_materials,id',
    'quantity' => 'required|numeric|min:0.001',
    'batch_number' => 'required|string|max:255',
    'received_date' => 'required|date',
    'notes' => 'nullable|string',
  ];

  public function mount()
  {
    $this->received_date = now()->format('Y-m-d');
    $this->stockIns = MaterialStockIn::with('RawMaterial')->get();
    // dd($this->stockIns);
  }

  public function save()
  {
    $this->validate();

    $user = Auth::user();

    // Check if user has warehouse manager or admin role
    // if (!$user->hasRole(['admin', 'warehouse_manager'])) {
    //   session()->flash('error', 'Unauthorized to perform stock-in operations.');
    //   return;
    // }

    MaterialStockIn::create([
      'raw_material_id' => $this->raw_material_id,
      'quantity' => $this->quantity,
      'batch_number' => $this->batch_number,
      'received_date' => $this->received_date,
      'received_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Material stock-in recorded successfully.');

    $this->reset(['raw_material_id', 'quantity', 'batch_number', 'notes']);
  }

  public function render()
  {
    $rawMaterials = RawMaterial::where('is_active', true)->get();

    return view('livewire.warehouse.stock-in', [
      'rawMaterials' => $rawMaterials,
    ]);
  }
}