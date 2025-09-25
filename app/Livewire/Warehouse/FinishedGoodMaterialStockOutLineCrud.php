<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\MaterialStockOutLine;
use App\Models\FinishedGoodMaterialStockOutLine;
use Livewire\WithPagination;

class FinishedGoodMaterialStockOutLineCrud extends Component
{
    use WithPagination;

    public $finished_good_id;
    public $usages = []; // array of [material_stock_out_line_id, quantity_used]
    public $link_id;
    public $isEdit = false;

    protected $rules = [
        'finished_good_id' => 'required|exists:finished_goods,id',
        'usages.*.material_stock_out_line_id' => 'required|exists:material_stock_out_lines,id',
        'usages.*.quantity_used' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        // initialize with one row
        $this->usages = [['material_stock_out_line_id' => '', 'quantity_used' => null]];
    }

    public function addUsageRow()
    {
        $this->usages[] = ['material_stock_out_line_id' => '', 'quantity_used' => null];
    }

    public function removeUsageRow($index)
    {
        unset($this->usages[$index]);
        $this->usages = array_values($this->usages); // reindex
    }

    public function create()
    {
        $this->validate();

        foreach ($this->usages as $usage) {
            FinishedGoodMaterialStockOutLine::create([
                'finished_good_id' => $this->finished_good_id,
                'material_stock_out_line_id' => $usage['material_stock_out_line_id'],
                'quantity_used' => $usage['quantity_used'],
            ]);
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $link = FinishedGoodMaterialStockOutLine::findOrFail($id);
        $this->link_id = $link->id;
        $this->finished_good_id = $link->finished_good_id;
        $this->usages = [[
            'material_stock_out_line_id' => $link->material_stock_out_line_id,
            'quantity_used' => $link->quantity_used,
        ]];
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        // update first usage (for simplicity in edit mode)
        $link = FinishedGoodMaterialStockOutLine::findOrFail($this->link_id);
        $link->update([
            'finished_good_id' => $this->finished_good_id,
            'material_stock_out_line_id' => $this->usages[0]['material_stock_out_line_id'],
            'quantity_used' => $this->usages[0]['quantity_used'],
        ]);

        $this->resetForm();
    }

    public function delete($id)
    {
        FinishedGoodMaterialStockOutLine::destroy($id);
    }

    private function resetForm()
    {
        $this->reset(['finished_good_id', 'link_id', 'isEdit']);
        $this->usages = [['material_stock_out_line_id' => '', 'quantity_used' => null]];
    }

    public function render()
    {
        $records = FinishedGoodMaterialStockOutLine::with([
            'finishedGood.product',
            'materialStockOutLine.materialStockOut.rawMaterial',
            'materialStockOutLine.productionLine'
        ])->paginate(8);

        $finishedGoods = FinishedGood::with('product')->get();
        $stockOutLines = MaterialStockOutLine::with([
            'materialStockOut.rawMaterial',
            'productionLine'
        ])->latest()->take(50)->get();

        return view('livewire.warehouse.finished-good-material-stock-out-line-crud', [
            'finishedGoods' => $finishedGoods,
            'stockOutLines' => $stockOutLines,
            'records' => $records
        ]);
    }
}
