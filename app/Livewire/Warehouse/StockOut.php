<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockOut;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StockOut extends Component
{
    use WithPagination;

    public $raw_material_id;

    public $quantity;

    public $batch_number;

    public $issued_date;

    public $notes;

    public $edit_id;

    public $is_editing = false;

    public $delete_id;

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
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Check if sufficient stock is available
        $rawMaterial = RawMaterial::find($this->raw_material_id);

        // For create, check current stock
        if (! $this->is_editing && $rawMaterial->quantity < $this->quantity) {
            session()->flash('error', 'Insufficient stock available. Current stock: '.$rawMaterial->quantity.' '.$rawMaterial->unit);

            return;
        }

        if ($this->is_editing) {
            // Update existing record
            $stockOut = MaterialStockOut::find($this->edit_id);

            // Calculate quantity difference for stock adjustment
            $quantity_diff = $this->quantity - $stockOut->quantity;

            // Check if sufficient stock for the difference
            if ($rawMaterial->quantity < $quantity_diff) {
                session()->flash('error', 'Insufficient stock available for update. Current stock: '.$rawMaterial->quantity.' '.$rawMaterial->unit.', Required additional: '.$quantity_diff.' '.$rawMaterial->unit);

                return;
            }

            // Update stock out record
            $stockOut->update([
                'raw_material_id' => $this->raw_material_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'issued_date' => $this->issued_date,
                'notes' => $this->notes,
            ]);

            // Adjust raw material quantity
            $rawMaterial->quantity -= $quantity_diff;
            $rawMaterial->save();

            session()->flash('message', 'Stock out record updated successfully.');
        } else {
            // Create new record
            MaterialStockOut::create([
                'raw_material_id' => $this->raw_material_id,
                'quantity' => $this->quantity,
                'batch_number' => $this->batch_number,
                'issued_date' => $this->issued_date,
                'issued_by' => $user->id,
                'status' => 'material_on_process',
                'notes' => $this->notes,
            ]);

            // Update raw material quantity
            $rawMaterial->quantity -= $this->quantity;
            $rawMaterial->save();

            session()->flash('message', 'Material stock-out recorded successfully.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $stockOut = MaterialStockOut::findOrFail($id);

        $this->edit_id = $stockOut->id;
        $this->raw_material_id = $stockOut->raw_material_id;
        $this->quantity = $stockOut->quantity;
        $this->batch_number = $stockOut->batch_number;
        $this->issued_date = $stockOut->issued_date;
        $this->notes = $stockOut->notes;
        $this->is_editing = true;

        // Scroll to form
        $this->dispatch('scroll-to-form');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function setDeleteId($id)
    {
        $this->delete_id = $id;
    }

    public function delete()
    {
        $stockOut = MaterialStockOut::findOrFail($this->delete_id);

        // Restore raw material quantity
        $rawMaterial = $stockOut->rawMaterial;
        $rawMaterial->quantity += $stockOut->quantity;
        $rawMaterial->save();

        // Delete the stock out record
        $stockOut->delete();

        $this->delete_id = null;
        session()->flash('message', 'Stock out record deleted successfully.');
    }

    public function updateStatus($id, $status)
    {
        $stockOut = MaterialStockOut::findOrFail($id);
        $stockOut->update(['status' => $status]);

        session()->flash('message', 'Status updated successfully.');
    }

    private function resetForm()
    {
        $this->reset([
            'raw_material_id',
            'quantity',
            'batch_number',
            'notes',
            'edit_id',
            'is_editing',
            'delete_id',
        ]);
        $this->issued_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $rawMaterials = RawMaterial::get();

        $stockOuts = MaterialStockOut::with(['rawMaterial', 'issuedBy'])
            ->latest()
            ->paginate(10);

        return view('livewire.warehouse.stock-out', [
            'rawMaterials' => $rawMaterials,
            'stockOuts' => $stockOuts,
        ]);
    }
}
