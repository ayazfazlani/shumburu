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

    // Form properties
    public $raw_material_id;
    public $quantity;
    public $batch_number;
    public $received_date;
    public $notes;

    // Edit state
    public $edit_id;
    public $is_editing = false;

    // Delete state
    public $delete_id;
    public $showDeleteModal = false;
    public $deleteQuantity = 0;
    public $deleteMaterialName = '';

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
        $this->showDeleteModal = false;
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if ($this->is_editing) {
            // Update existing record
            $stockIn = MaterialStockIn::findOrFail($this->edit_id);
            $oldQuantity = $stockIn->quantity;
            $oldRawMaterialId = $stockIn->raw_material_id;

            // Update stock in record
            $stockIn->update([
                'raw_material_id' => $this->raw_material_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'received_date' => $this->received_date,
                'notes' => $this->notes,
            ]);

            // Adjust raw material quantities
            RawMaterial::$skipAutoTransaction = true;
            if ($oldRawMaterialId == $this->raw_material_id) {
                // Same material - adjust quantity difference
                $oldRawMaterial = RawMaterial::find($oldRawMaterialId);
                $quantityDiff = $this->quantity - $oldQuantity;
                $oldRawMaterial->quantity += $quantityDiff;
                $oldRawMaterial->save();
            } else {
                // Different material - restore old, deduct new
                $oldRawMaterial = RawMaterial::find($oldRawMaterialId);
                $oldRawMaterial->quantity += $oldQuantity;
                $oldRawMaterial->save();

                $newRawMaterial = RawMaterial::find($this->raw_material_id);
                $newRawMaterial->quantity -= $this->quantity;
                $newRawMaterial->save();
            }
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Stock in record updated successfully.');
        } else {
            // Create stock in transaction
            MaterialStockIn::create([
                'raw_material_id' => $this->raw_material_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'received_date' => $this->received_date,
                'received_by' => $user->id,
                'notes' => $this->notes,
            ]);

            // Update raw material quantity
            RawMaterial::$skipAutoTransaction = true;
            $rawMaterial = RawMaterial::find($this->raw_material_id);
            $rawMaterial->quantity += $this->quantity;
            $rawMaterial->save();
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Material stock-in recorded successfully.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function edit($id)
    {
        $stockIn = MaterialStockIn::findOrFail($id);

        $this->edit_id = $stockIn->id;
        $this->raw_material_id = $stockIn->raw_material_id;
        $this->quantity = $stockIn->quantity;
        $this->batch_number = $stockIn->batch_number;
        $this->received_date = $stockIn->received_date->format('Y-m-d');
        $this->notes = $stockIn->notes;
        $this->is_editing = true;

        $this->dispatch('scroll-to-form');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function setDeleteId($id)
    {
        $stockIn = MaterialStockIn::findOrFail($id);
        $this->delete_id = $id;
        $this->deleteQuantity = $stockIn->quantity;
        $this->deleteMaterialName = $stockIn->rawMaterial->name ?? 'N/A';
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->delete_id = null;
        $this->deleteQuantity = 0;
        $this->deleteMaterialName = '';
    }

    public function delete()
    {
        if ($this->delete_id) {
            $stockIn = MaterialStockIn::findOrFail($this->delete_id);

            // Restore raw material quantity (skip auto-transaction)
            RawMaterial::$skipAutoTransaction = true;
            $rawMaterial = $stockIn->rawMaterial;
            $rawMaterial->quantity -= $stockIn->quantity;
            $rawMaterial->save();
            RawMaterial::$skipAutoTransaction = false;

            // Delete the stock in record
            $stockIn->delete();

            session()->flash('message', 'Stock in record deleted successfully.');
        }

        $this->closeDeleteModal();
    }

    private function resetForm()
    {
        $this->reset(['raw_material_id', 'quantity', 'batch_number', 'notes', 'edit_id', 'is_editing', 'delete_id']);
        $this->received_date = now()->format('Y-m-d');
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $rawMaterials = RawMaterial::where('is_active', true)->get();
        $stockIns = MaterialStockIn::with('rawMaterial', 'receivedBy')->latest()->paginate(10);

        return view('livewire.warehouse.stock-in', [
            'rawMaterials' => $rawMaterials,
            'stockIns' => $stockIns,
        ]);
    }
}
