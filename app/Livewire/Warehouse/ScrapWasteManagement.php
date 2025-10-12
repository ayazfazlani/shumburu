<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\ScrapWaste;
use App\Models\RawMaterial;
use Livewire\WithPagination;
use App\Models\FinishedGood;
use Illuminate\Support\Facades\Auth;

class ScrapWasteManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;

    // Form fields
    public $date;
    public $scrap_type = 'raw_material'; // 'raw_material' or 'finished_goods'
    public $material_stock_out_line_id;
    public $finished_good_id;
    public $quantity;
    public $reason;
    public $notes;
    public $is_repressible = false;
    public $disposal_method = 'dispose';
    public $cost;

    protected $rules = [
        'date' => 'required|date',
        'scrap_type' => 'required|in:raw_material,finished_goods',
        'material_stock_out_line_id' => 'required_if:scrap_type,raw_material|exists:material_stock_out_lines,id',
        'finished_good_id' => 'required_if:scrap_type,finished_goods|exists:finished_goods,id',
        'quantity' => 'required|numeric|min:0.001',
        'reason' => 'required|string|max:255',
        'notes' => 'nullable|string',
        'is_repressible' => 'boolean',
        'disposal_method' => 'required|in:dispose,reprocess,return_to_supplier',
        'cost' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        $scrapWasteRecords = ScrapWaste::with([
                'materialStockOutLine.materialStockOut.rawMaterial',
                'materialStockOutLine.productionLine',
                'finishedGood.product',
                'recordedBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stockOutLines = \App\Models\MaterialStockOutLine::with(['materialStockOut.rawMaterial', 'productionLine'])->get();
        $finishedGoods = FinishedGood::with('product')->get();

        return view('livewire.warehouse.scrap-waste-management', [
            'scrapWasteRecords' => $scrapWasteRecords,
            'stockOutLines' => $stockOutLines,
            'finishedGoods' => $finishedGoods,
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
        $record = ScrapWaste::findOrFail($id);
        $this->editingId = $id;
        $this->date = $record->waste_date;
        $this->scrap_type = $record->scrap_type;
        $this->material_stock_out_line_id = $record->material_stock_out_line_id;
        $this->finished_good_id = $record->finished_good_id;
        $this->quantity = $record->quantity;
        $this->reason = $record->reason;
        $this->notes = $record->notes;
        $this->is_repressible = $record->is_repressible;
        $this->disposal_method = $record->disposal_method;
        $this->cost = $record->cost;

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'waste_date' => $this->date,
            'scrap_type' => $this->scrap_type,
            'material_stock_out_line_id' => $this->scrap_type === 'raw_material' ? $this->material_stock_out_line_id : null,
            'finished_good_id' => $this->scrap_type === 'finished_goods' ? $this->finished_good_id : null,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'recorded_by' => Auth::id(),
            'notes' => $this->notes,
            'is_repressible' => $this->is_repressible,
            'disposal_method' => $this->disposal_method,
            'cost' => $this->cost,
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
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Scrap/Waste record approved successfully.');
    }

    public function reject($id)
    {
        $record = ScrapWaste::findOrFail($id);
        $record->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
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
        $this->scrap_type = 'raw_material';
        $this->material_stock_out_line_id = '';
        $this->finished_good_id = '';
        $this->quantity = '';
        $this->reason = '';
        $this->notes = '';
        $this->is_repressible = false;
        $this->disposal_method = 'dispose';
        $this->cost = '';
        $this->resetValidation();
    }

    // Get waste ratio for a specific date
    public function getWasteRatio($date)
    {
        $totalProduction = FinishedGood::whereDate('created_at', $date)->sum('total_weight');
        $totalWaste = ScrapWaste::whereDate('waste_date', $date)->sum('quantity');
        
        if ($totalProduction > 0) {
            return round(($totalWaste / $totalProduction) * 100, 2);
        }
        
        return 0;
    }
}
