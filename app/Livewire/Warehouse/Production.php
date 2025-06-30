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
    public $material_stock_out_id;
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

    public function save()
    {
        $this->validate([
            'production_line_id' => 'required|exists:production_lines,id',
            'product_id' => 'required|exists:products,id',
            'material_stock_out_id' => 'required|exists:material_stock_outs,id',
            'shift' => 'nullable|string|max:10',
            'finishedGoods.*.type' => 'required|in:roll,cut',
            'finishedGoods.*.length_m' => 'required|numeric|min:1',
            'finishedGoods.*.quantity' => 'required|integer|min:1',
            // Add validation for other finished good fields as needed
        ]);

        DB::transaction(function () {
            // 1. Create MaterialStockOutLine (minimal fields)
            $line = MaterialStockOutLine::create([
                'material_stock_out_id' => $this->material_stock_out_id,
                'production_line_id' => $this->production_line_id,
                'quantity_consumed' => 0, // You can sum from finished goods if needed
                'shift' => $this->shift,
            ]);

            // 2. Add Finished Goods (all fields from finished_goods table)
            foreach ($this->finishedGoods as $fg) {
                $product = Product::find($this->product_id);
                $weight_per_meter = $product->weight_per_meter ?? 0;
                $total_weight = $fg['quantity'] * $fg['length_m'] * $weight_per_meter;

                FinishedGood::create([
                    'material_stock_out_line_id' => $line->id,
                    'product_id' => $this->product_id,
                    'type' => $fg['type'],
                    'length_m' => $fg['length_m'],
                    'outer_diameter' => $fg['outer_diameter'] ?: null,
                    'quantity' => $fg['quantity'],
                    'total_weight' => $total_weight,
                    'size' => $fg['size'] ?: null,
                    'surface' => $fg['surface'] ?: null,
                    'thickness' => $fg['thickness'] ?: null,
                    'ovality' => $fg['ovality'] ?: null,
                    'batch_number' => $fg['batch_number'] ?: $this->generateBatchNumber(),
                    'production_date' => $fg['production_date'] ?: now()->toDateString(),
                    'purpose' => $fg['purpose'] ?: 'for_stock',
                    'customer_id' => $fg['customer_id'] ?: null,
                    'produced_by' => Auth::id(),
                    'notes' => $fg['notes'] ?: null,
                ]);
            }

            // 3. Add Scraps (if needed)
            foreach ($this->scraps as $scrap) {
                if ($scrap['quantity']) {
                    ScrapWaste::create([
                        'material_stock_out_line_id' => $line->id,
                        'quantity' => $scrap['quantity'],
                        'reason' => $scrap['reason'],
                        'waste_date' => now(),
                        'recorded_by' => Auth::id(),
                        'notes' => $scrap['notes'],
                    ]);
                }
            }
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
            'stockOuts' => MaterialStockOut::all(),
            'customers' => Customer::all(),
        ]);
    }
}
