<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockOut;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StockOut extends Component
{
  public $raw_material_id;
  public $quantity;
  public $batch_number;
  public $issued_date;
  public $notes;
  public $stockOuts;

  protected $rules = [
    'raw_material_id' => 'required|exists:raw_materials,id',
    'quantity' => 'required|numeric|min:0.001',
    'batch_number' => 'required|string|max:255',
    'issued_date' => 'required|date',
    'notes' => 'nullable|string',
  ];

  public function mount()
  {
    $this->issued_date = now()->format('Y-m-d');
    $this->stockOuts = MaterialStockOut::with(['rawMaterial', 'issuedBy'])->get();
    // dd($this->stockOuts);
  }

  public function save()
  {

    $this->validate();

    $user = Auth::user();


    MaterialStockOut::create([
      'raw_material_id' => $this->raw_material_id,
      'quantity' => $this->quantity,
      'batch_number' => $this->batch_number,
      'issued_date' => $this->issued_date,
      'issued_by' => $user->id,
      'status' => 'material_on_process',
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Material stock-out recorded successfully.');

    $this->reset(['raw_material_id', 'quantity', 'batch_number', 'notes']);
  }

  public function render()
  {
    $rawMaterials = RawMaterial::get();
    $this->stockOuts = MaterialStockOut::with(['rawMaterial', 'issuedBy'])->get();
    return view('livewire.warehouse.stock-out', [
      'rawMaterials' => $rawMaterials,
    ]);
  }
}
