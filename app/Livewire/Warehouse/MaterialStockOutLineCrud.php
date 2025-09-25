<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\MaterialStockOutLine;
use App\Models\MaterialStockOut;
use App\Models\ProductionLine;

class MaterialStockOutLineCrud extends Component
{
    public $lines;
    public $shift;
    public $production_line_id;
    public $materials = []; // holds multiple rows of [material_stock_out_id, quantity]

    public $materialStockOutLines = [];
    public $isEdit = false;
    public $editLineId;

    protected $rules = [
        'shift' => 'required|in:A,B',
        'production_line_id' => 'required|exists:production_lines,id',
        'materials.*.material_stock_out_id' => 'required|exists:material_stock_outs,id',
        'materials.*.quantity_consumed' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->fetch();
        $this->materials = [
            ['material_stock_out_id' => '', 'quantity_consumed' => '']
        ];
    }

    public function fetch()
    {
        $this->materialStockOutLines = MaterialStockOutLine::with([
            'materialStockOut.rawMaterial',
            'productionLine'
        ])->latest()->get();

        $this->lines = ProductionLine::all();
    }

    public function addRow()
    {
        $this->materials[] = ['material_stock_out_id' => '', 'quantity_consumed' => ''];
    }

    public function removeRow($index)
    {
        unset($this->materials[$index]);
        $this->materials = array_values($this->materials);
    }

    public function saveBatch()
    {
        $this->validate();

        foreach ($this->materials as $row) {
            MaterialStockOutLine::create([
                'material_stock_out_id' => $row['material_stock_out_id'],
                'production_line_id' => $this->production_line_id,
                'quantity_consumed' => $row['quantity_consumed'],
                'shift' => $this->shift
            ]);
        }

        $this->reset(['shift', 'production_line_id', 'materials']);
        $this->materials = [['material_stock_out_id' => '', 'quantity_consumed' => '']];
        $this->fetch();
    }

    public function delete($id)
    {
        MaterialStockOutLine::destroy($id);
        $this->fetch();
    }

    public function render()
    {
        $this->fetch();
        $stockOuts = MaterialStockOut::with('rawMaterial')->get();

        return view('livewire.warehouse.material-stock-out-line-crud', [
            'stockOuts' => $stockOuts,
            'lines' => $this->lines,
            'materialStockOutLines' => $this->materialStockOutLines,
        ]);
    }
}
