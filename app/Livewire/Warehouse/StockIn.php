<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\RawMaterial;
use Livewire\WithPagination;

use App\Models\MaterialStockIn;
use Illuminate\Support\Facades\Auth;

class StockIn extends Component
{
  use WithPagination;
  
  protected $paginationTheme = 'tailwind';
  
  public $raw_material_id;
  public $quantity;
  public $batch_number;
  public $received_date;
  public $notes;
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
    // Remove manual stockIns assignment - let render() handle it
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
    
    // Reset to first page to show the new record
    $this->resetPage();
  }

  public function render()
  {
    // Get raw materials for the dropdown (not stock ins)
    $rawMaterials = RawMaterial::where('is_active', true)->get();
    
    // Get stock ins with pagination (don't call toArray())
    $stockIns = MaterialStockIn::with('rawMaterial', 'receivedBy')->paginate(10);
    // dd($stockIns);
    
    return view('livewire.warehouse.stock-in', [
      'rawMaterials' => $rawMaterials, 
      'stockIns' => $stockIns,
    ]);
  }
}