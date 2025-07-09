<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\ScrapWaste;
use App\Models\ProductionLine;
use Livewire\Attributes\Layout;
use App\Models\MaterialStockOut;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\Auth;
use App\Models\FinishedGood;
use App\Models\Customer;

class Production extends Component
{


    public $production_line_id;
    public $product_id;
    public $material_stock_out_line_ids = [];
    public $shift;
    public $size;
    public $surface;
    public $thickness;
    public $outer_diameter;
    public $ovality;
    public $notes;

    // Dynamic arrays for finished goods and scraps
    public $finishedGoods = [];
    public $scraps = [];

    public $customers;

    public $stockOutUsages = [
        // ['stock_out_line_id' => null, 'quantity_used' => null]
    ];

    public $stockOutScraps = [
        // ['stock_out_line_id' => null, 'quantity_scrapped' => null]
    ];

    public function mount()
    {
        $this->finishedGoods = [
            ['type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => '']
        ];
        $this->scraps = [
            ['quantity' => '', 'reason' => '', 'notes' => '']
        ];
    }

    public function addFinishedGood()
    {
        $this->finishedGoods[] = ['type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => ''];
    }

    public function removeFinishedGood($index)
    {
        unset($this->finishedGoods[$index]);
        $this->finishedGoods = array_values($this->finishedGoods);
    }

    public function addScrap()
    {
        $this->scraps[] = ['quantity' => '', 'reason' => '', 'notes' => ''];
    }

    public function removeScrap($index)
    {
        unset($this->scraps[$index]);
        $this->scraps = array_values($this->scraps);
    }

    public function addStockOutUsage()
    {
        $this->stockOutUsages[] = [
            'stock_out_line_id' => null,
            'quantity_used' => null,
        ];
    }

    public function removeStockOutUsage($index)
    {
        unset($this->stockOutUsages[$index]);
        $this->stockOutUsages = array_values($this->stockOutUsages);
    }

    public function addStockOutScrap()
    {
        $this->stockOutScraps[] = [
            'stock_out_line_id' => null,
            'quantity_scrapped' => null,
        ];
    }

    public function removeStockOutScrap($index)
    {
        unset($this->stockOutScraps[$index]);
        $this->stockOutScraps = array_values($this->stockOutScraps);
    }

    public function save()
    {
        $this->validate([
            'production_line_id' => 'required|exists:production_lines,id',
            'stockOutUsages' => 'required|array|min:1',
            'stockOutUsages.*.stock_out_line_id' => 'required|exists:material_stock_out_lines,id',
            'stockOutUsages.*.quantity_used' => 'required|numeric|min:0.001',
            'stockOutScraps' => 'nullable|array',
            'stockOutScraps.*.stock_out_line_id' => 'required_with:stockOutScraps.*.quantity_scrapped|exists:material_stock_out_lines,id',
            'stockOutScraps.*.quantity_scrapped' => 'required_with:stockOutScraps.*.stock_out_line_id|numeric|min:0.001',
            'product_id' => 'required|exists:products,id',
            'shift' => 'nullable|string|max:10',
            'finishedGoods.*.type' => 'required|in:roll,cut',
            'finishedGoods.*.length_m' => 'required|numeric|min:1',
            'finishedGoods.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            // $production =
            //     \App\Models\Production::create([
            //         'reference' => 'PROD-' . strtoupper(uniqid()),
            //         'production_date' => now()->toDateString(),
            //         'notes' => $this->notes,
            //     ]);

            foreach ($this->stockOutUsages as $usage) {
                $line = MaterialStockOutLine::find($usage['stock_out_line_id']);
                $line->quantity_consumed = $usage['quantity_used'];
                $line->production_line_id = $this->production_line_id;
                $line->shift = $this->shift;
                $line->save();
            }

            foreach ($this->stockOutScraps as $scrap) {
                if ($scrap['stock_out_line_id'] && $scrap['quantity_scrapped']) {
                    \App\Models\ScrapWaste::create([
                        'material_stock_out_line_id' => $scrap['stock_out_line_id'],
                        'quantity' => $scrap['quantity_scrapped'],
                        'reason' => 'Production Scrap',
                        'waste_date' => now(),
                        'recorded_by' => Auth::id(),
                        'notes' => null,
                    ]);
                }
            }

            // Optionally, create finished goods as before
            // ...
        });

        session()->flash('success', 'Production entry saved successfully!');
        return redirect()->route('warehouse.production.index');
    }

    private function generateBatchNumber()
    {
        return 'BATCH-' . strtoupper(uniqid());
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.warehouse.production', [
            'lines' => ProductionLine::all(),
            'products' => Product::all(),
            'stockOutLines' => \App\Models\MaterialStockOutLine::with(['materialStockOut.rawMaterial', 'productionLine'])->get(),
            'customers' => Customer::all(),
        ]);
    }
}
