<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\MaterialStockOutLine;
use App\Models\MaterialStockOut;
use App\Models\ProductionLine;

class MaterialStockOutLineCrud extends Component
{
    public $lines, $material_stock_out_id, $production_line_id, $quantity_consumed, $line_id;
    public $shift;
    public $materialStockOutLines = [];
    public $isEdit = false;

    protected $rules = [
        'material_stock_out_id' => 'required|exists:material_stock_outs,id',
        'production_line_id' => 'required|exists:production_lines,id',
        'quantity_consumed' => 'required|numeric|min:0.01',
        'shift' => 'required|in:A,B'
    ];

    public function mount()
    {
        $this->fetch();
    }

    public function fetch()
    {
        $this->materialStockOutLines = MaterialStockOutLine::with([
            'materialStockOut.rawMaterial',
            'productionLine'
        ])->get();
    }

    public function create()
    {
        $this->validate();
        MaterialStockOutLine::create([
            'material_stock_out_id' => $this->material_stock_out_id,
            'production_line_id' => $this->production_line_id,
            'quantity_consumed' => $this->quantity_consumed,
            'shift' => $this->shift
        ]);
        $this->reset(['material_stock_out_id', 'production_line_id', 'quantity_consumed']);
        $this->fetch();
    }

    public function edit($id)
    {
        $line = MaterialStockOutLine::findOrFail($id);
        $this->line_id = $line->id;
        $this->material_stock_out_id = $line->material_stock_out_id;
        $this->production_line_id = $line->production_line_id;
        $this->quantity_consumed = $line->quantity_consumed;
        $this->isEdit = true;
        $this->shift = $line->shift;
    }

    public function update()
    {
        $this->validate();
        $line = MaterialStockOutLine::findOrFail($this->line_id);
        $line->update([
            'material_stock_out_id' => $this->material_stock_out_id,
            'production_line_id' => $this->production_line_id,
            'quantity_consumed' => $this->quantity_consumed,
            'shift' => $this->shift
        ]);
        $this->reset(['material_stock_out_id', 'production_line_id', 'quantity_consumed', 'line_id', 'isEdit']);
        $this->fetch();
    }

    public function delete($id)
    {
        MaterialStockOutLine::destroy($id);
        $this->fetch();
    }

    public function render()
    {
        $this->mount();
        $stockOuts = MaterialStockOut::with('rawMaterial')->latest()->take(5)->get();
        $this->lines = ProductionLine::all();
        return view('livewire.warehouse.material-stock-out-line-crud', [
            'stockOuts' => $stockOuts,
            'lines' => $this->lines,
            'materialStockOutLines' => $this->materialStockOutLines,
        ]);
    }
} 