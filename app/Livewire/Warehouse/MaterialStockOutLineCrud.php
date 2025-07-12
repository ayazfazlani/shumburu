<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\MaterialStockOutLine;
use App\Models\MaterialStockOut;
use App\Models\ProductionLine;

class MaterialStockOutLineCrud extends Component
{
    public $lines, $material_stock_out_id, $production_line_id, $quantity_consumed, $line_id;
    public $isEdit = false;

    protected $rules = [
        'material_stock_out_id' => 'required|exists:material_stock_outs,id',
        'production_line_id' => 'required|exists:production_lines,id',
        'quantity_consumed' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->fetch();
    }

    public function fetch()
    {
        $this->lines = MaterialStockOutLine::with([
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
    }

    public function update()
    {
        $this->validate();
        $line = MaterialStockOutLine::findOrFail($this->line_id);
        $line->update([
            'material_stock_out_id' => $this->material_stock_out_id,
            'production_line_id' => $this->production_line_id,
            'quantity_consumed' => $this->quantity_consumed,
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
        $stockOuts = MaterialStockOut::with('rawMaterial')->get();
        $lines = ProductionLine::all();
        return view('livewire.warehouse.material-stock-out-line-crud', [
            'stockOuts' => $stockOuts,
            'lines' => $lines,
            'materialStockOutLines' => $this->lines,
        ]);
    }
} 