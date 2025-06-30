<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\ScrapWaste;
use App\Models\RawMaterial;
use Livewire\WithPagination;

use Illuminate\Support\Facades\Auth;

class ScrapWasteRecord extends Component
{

    use WithPagination;

    public $showForm = false;
    public $editingId = null;

    // Form fields
    public $date;
    public $quantity;
    public $reason;
    public $notes;
    public $material_stock_out_id;
    public $production_line_id;
    public $quantity_used;

    protected $rules = [
        'date' => 'required|date',
        'quantity' => 'required|numeric|min:0',
        'reason' => 'required|string|max:255',
        'notes' => 'nullable|string',
        'material_stock_out_id' => 'required|exists:material_stock_outs,id',
        'production_line_id' => 'required|exists:production_lines,id',
        'quantity_used' => 'required|numeric|min:0.001',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        $scrapWasteRecords = ScrapWaste::with(['materialStockOutLine.materialStockOut', 'materialStockOutLine.productionLine', 'recordedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stockOuts = \App\Models\MaterialStockOut::all();
        $lines = \App\Models\ProductionLine::all();

        return view('livewire.warehouse.scrap-waste-record', [
            'scrapWasteRecords' => $scrapWasteRecords,
            'stockOuts' => $stockOuts,
            'lines' => $lines,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit($id)
    {
        $record = ScrapWaste::with('materialStockOutLine')->findOrFail($id);
        // dd($record);
        $this->editingId = $id;
        $this->date = $record->waste_date;
        $this->quantity = $record->quantity;
        $this->reason = $record->reason;
        $this->notes = $record->notes;
        $this->material_stock_out_id = $record->materialStockOutLine->material_stock_out_id;
        $this->production_line_id = $record->materialStockOutLine->production_line_id;
        $this->quantity_used = $record->materialStockOutLine->quantity_consumed;

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $stockOutLine = \App\Models\MaterialStockOutLine::create([
            'material_stock_out_id' => $this->material_stock_out_id,
            'production_line_id' => $this->production_line_id,
            'quantity_consumed' => $this->quantity_used,
        ]);

        $data = [
            'waste_date' => $this->date,
            'material_stock_out_line_id' => $stockOutLine->id,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'recorded_by' => Auth::id(),
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            $record = ScrapWaste::findOrFail($this->editingId);
            $record->update($data);
            session()->flash('message', 'Scrap/Waste record updated successfully.');
        } else {
            ScrapWaste::create($data);
            session()->flash('message', 'Scrap/Waste record created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $record = ScrapWaste::findOrFail($id);
        $record->delete();
        session()->flash('message', 'Scrap/Waste record deleted successfully.');
    }

    public function approve($id)
    {
        $record = ScrapWaste::findOrFail($id);
        $record->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Scrap/Waste record approved successfully.');
    }

    public function reject($id)
    {
        $record = ScrapWaste::findOrFail($id);
        $record->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Scrap/Waste record rejected successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->date = now()->format('Y-m-d');
        $this->quantity = '';
        $this->reason = '';
        $this->notes = '';
        $this->material_stock_out_id = '';
        $this->production_line_id = '';
        $this->quantity_used = '';
        $this->resetValidation();
    }
}