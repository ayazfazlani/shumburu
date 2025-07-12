<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\MaterialStockOutLine;
use App\Models\FinishedGoodMaterialStockOutLine;

class FinishedGoodMaterialStockOutLineCrud extends Component
{
    public $links, $finished_good_id, $material_stock_out_line_id, $quantity_used, $link_id;
    public $isEdit = false;

    protected $rules = [
        'finished_good_id' => 'required|exists:finished_goods,id',
        'material_stock_out_line_id' => 'required|exists:material_stock_out_lines,id',
        'quantity_used' => 'nullable|numeric',
    ];

    public function mount()
    {
        $this->fetch();
    }

    public function fetch()
    {
        $this->links = FinishedGoodMaterialStockOutLine::with([
            'finishedGood.product',
            'materialStockOutLine.materialStockOut.rawMaterial',
            'materialStockOutLine.productionLine'
        ])->get();
    }

    public function create()
    {
        $this->validate();
        FinishedGoodMaterialStockOutLine::create([
            'finished_good_id' => $this->finished_good_id,
            'material_stock_out_line_id' => $this->material_stock_out_line_id,
            'quantity_used' => $this->quantity_used,
        ]);
        $this->reset(['finished_good_id', 'material_stock_out_line_id', 'quantity_used']);
        $this->fetch();
    }

    public function edit($id)
    {
        $link = FinishedGoodMaterialStockOutLine::findOrFail($id);
        $this->link_id = $link->id;
        $this->finished_good_id = $link->finished_good_id;
        $this->material_stock_out_line_id = $link->material_stock_out_line_id;
        $this->quantity_used = $link->quantity_used;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();
        $link = FinishedGoodMaterialStockOutLine::findOrFail($this->link_id);
        $link->update([
            'finished_good_id' => $this->finished_good_id,
            'material_stock_out_line_id' => $this->material_stock_out_line_id,
            'quantity_used' => $this->quantity_used,
        ]);
        $this->reset(['finished_good_id', 'material_stock_out_line_id', 'quantity_used', 'link_id', 'isEdit']);
        $this->fetch();
    }

    public function delete($id)
    {
        FinishedGoodMaterialStockOutLine::destroy($id);
        $this->fetch();
    }

    public function render()
    {
        $finishedGoods = FinishedGood::with('product')->get();
        $stockOutLines = MaterialStockOutLine::with([
            'materialStockOut.rawMaterial',
            'productionLine'
        ])->get();
        return view('livewire.warehouse.finished-good-material-stock-out-line-crud', [
            'finishedGoods' => $finishedGoods,
            'stockOutLines' => $stockOutLines,
        ]);
    }
} 