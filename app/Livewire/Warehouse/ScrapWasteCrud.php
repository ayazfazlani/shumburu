<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockOutLine;
use Livewire\Component;
use App\Models\ScrapWaste;

class ScrapWasteCrud extends Component
{
    public $scrapWastes, $material_stock_out_line_id, $reason, $waste_date, $recorded_by, $notes, $scrap_waste_id;
    public $isEdit = false;
    public $materialStockOutLines;

    protected $rules = [
        'material_stock_out_line_id' => 'required|exists:material_stock_out_lines,id',
        'reason' => 'required|string',
        'waste_date' => 'required|date',
        'recorded_by' => 'required|integer',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->fetch();
    }

    public function fetch()
    {


        $this->scrapWastes = ScrapWaste::with('materialStockOutLine')->get();
    }

    public function create()
    {
        $this->validate();
        ScrapWaste::create([
            'material_stock_out_line_id' => $this->material_stock_out_line_id,
            'reason' => $this->reason,
            'waste_date' => $this->waste_date,
            'recorded_by' => $this->recorded_by,
            'notes' => $this->notes,
        ]);
        $this->reset(['material_stock_out_line_id', 'reason', 'waste_date', 'recorded_by', 'notes']);
        $this->fetch();
    }

    public function edit($id)
    {
        $sw = ScrapWaste::findOrFail($id);
        $this->scrap_waste_id = $sw->id;
        $this->material_stock_out_line_id = $sw->material_stock_out_line_id;
        $this->reason = $sw->reason;
        $this->waste_date = $sw->waste_date;
        $this->recorded_by = $sw->recorded_by;
        $this->notes = $sw->notes;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();
        $sw = ScrapWaste::findOrFail($this->scrap_waste_id);
        $sw->update([
            'material_stock_out_line_id' => $this->material_stock_out_line_id,
            'reason' => $this->reason,
            'waste_date' => $this->waste_date,
            'recorded_by' => $this->recorded_by,
            'notes' => $this->notes,
        ]);
        $this->reset(['material_stock_out_line_id', 'reason', 'waste_date', 'recorded_by', 'notes', 'scrap_waste_id', 'isEdit']);
        $this->fetch();
    }

    public function delete($id)
    {
        ScrapWaste::destroy($id);
        $this->fetch();
    }

    public function render()
    {
        $this->materialStockOutLines = MaterialStockOutLine::all();
        return view('livewire.warehouse.scrap-waste-crud');
    }
} 