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
    public $material_stock_out_id;
    public $production_line_id;
    public $quantity_consumed;
    public $shift;
    public $finishedGoods = [];
    public $scraps = [];
    public $customers;
    public $stockOutUsages = [];
    public $stockOutScraps = [];
    public $showModal = false;
    public $isEdit = false;
    public $editingId = null;
    public $productions;

    public function mount()
    {
        $this->finishedGoods = [
            ['product_id' => '', 'type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => '']
        ];
        $this->scraps = [
            ['quantity' => '', 'reason' => '', 'notes' => '']
        ];
        $this->stockOutUsages = [];
        $this->stockOutScraps = [];
    }

    public function addFinishedGood()
    {
        $this->finishedGoods[] = ['product_id' => '', 'type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => ''];
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

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $entry = MaterialStockOutLine::with(['productionLine', 'materialStockOut', 'finishedGoods'])->findOrFail($id);
        $this->editingId = $id;
        $this->material_stock_out_id = $entry->material_stock_out_id;
        $this->production_line_id = $entry->production_line_id;
        $this->quantity_consumed = $entry->quantity_consumed;
        $this->shift = $entry->shift;
        
        // Load finished goods
        $this->finishedGoods = [];
        foreach ($entry->finishedGoods as $fg) {
            $this->finishedGoods[] = [
                'product_id' => $fg->product_id,
                'type' => $fg->type,
                'length_m' => $fg->length_m,
                'quantity' => $fg->quantity,
                'outer_diameter' => $fg->outer_diameter,
                'size' => $fg->size,
                'surface' => $fg->surface,
                'thickness' => $fg->thickness,
                'ovality' => $fg->start_ovality,
                'batch_number' => $fg->batch_number,
                'production_date' => $fg->production_date,
                'purpose' => $fg->purpose,
                'customer_id' => $fg->customer_id,
                'notes' => $fg->notes,
            ];
        }
        
        // If no finished goods, add one empty row
        if (empty($this->finishedGoods)) {
            $this->finishedGoods = [
                ['product_id' => '', 'type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => '']
            ];
        }
        
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $entry = MaterialStockOutLine::findOrFail($id);
            
            // Delete associated finished goods
            $entry->finishedGoods()->delete();
            
            // Delete the entry
            $entry->delete();
        });
        
        session()->flash('success', 'Production entry deleted successfully!');
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->material_stock_out_id = null;
        $this->production_line_id = null;
        $this->quantity_consumed = null;
        $this->shift = null;
        $this->finishedGoods = [
            ['product_id' => '', 'type' => 'roll', 'length_m' => '', 'quantity' => '', 'outer_diameter' => '', 'size' => '', 'surface' => '', 'thickness' => '', 'ovality' => '', 'batch_number' => '', 'production_date' => '', 'purpose' => 'for_stock', 'customer_id' => '', 'notes' => '']
        ];
        $this->stockOutUsages = [];
        $this->stockOutScraps = [];
    }

    public function save()
    {
        $this->validate([
            'material_stock_out_id' => 'required|exists:material_stock_outs,id',
            'production_line_id' => 'required|exists:production_lines,id',
            'quantity_consumed' => 'required|numeric|min:0.001',
            'shift' => 'nullable|string|max:10',
            'finishedGoods.*.product_id' => 'required|exists:products,id',
            'finishedGoods.*.type' => 'required|in:roll,cut',
            'finishedGoods.*.length_m' => 'required|numeric|min:1',
            'finishedGoods.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            if ($this->isEdit && $this->editingId) {
                $entry = MaterialStockOutLine::findOrFail($this->editingId);
                $entry->update([
                    'material_stock_out_id' => $this->material_stock_out_id,
                    'production_line_id' => $this->production_line_id,
                    'quantity_consumed' => $this->quantity_consumed,
                    'shift' => $this->shift,
                ]);
                
                // Delete existing finished goods for this entry
                $entry->finishedGoods()->delete();
            } else {
                $entry = MaterialStockOutLine::create([
                    'material_stock_out_id' => $this->material_stock_out_id,
                    'production_line_id' => $this->production_line_id,
                    'quantity_consumed' => $this->quantity_consumed,
                    'shift' => $this->shift,
                ]);
            }
            
            // Save finished goods with proper relationship
            foreach ($this->finishedGoods as $fg) {
                if (!empty($fg['product_id']) && !empty($fg['quantity'])) {
                    $finishedGood = FinishedGood::create([
                        'product_id' => $fg['product_id'],
                        'quantity' => $fg['quantity'],
                        'batch_number' => $fg['batch_number'],
                        'production_date' => $fg['production_date'],
                        'purpose' => $fg['purpose'],
                        'customer_id' => $fg['customer_id'],
                        'produced_by' => Auth::id(),
                        'notes' => $fg['notes'],
                        'type' => $fg['type'],
                        'length_m' => $fg['length_m'],
                        'outer_diameter' => $fg['outer_diameter'],
                        'size' => $fg['size'],
                        'surface' => $fg['surface'],
                        'thickness' => $fg['thickness'],
                        'start_ovality' => $fg['ovality'],
                        'end_ovality' => $fg['ovality'],
                        'total_weight' => $this->calculateWeight($fg),
                    ]);
                    
                    // Link finished good to material stock out line
                    $entry->finishedGoods()->attach($finishedGood->id, [
                        'quantity_used' => $this->quantity_consumed,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        $this->showModal = false;
        session()->flash('success', 'Production entry saved successfully!');
    }
    
    private function calculateWeight($finishedGood)
    {
        // Calculate weight based on length, quantity, and product specifications
        $length = (float) ($finishedGood['length_m'] ?? 0);
        $quantity = (float) ($finishedGood['quantity'] ?? 0);
        
        // Get product weight per meter if available
        $product = Product::find($finishedGood['product_id']);
        $weightPerMeter = $product->weight_per_meter ?? 1.0; // Default weight per meter
        
        return $length * $quantity * $weightPerMeter;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $this->productions = MaterialStockOutLine::with(['productionLine', 'materialStockOut', 'finishedGoods'])->get();
        return view('livewire.warehouse.production', [
            'lines' => ProductionLine::all(),
            'products' => Product::all(),
            'stockOuts' => MaterialStockOut::all(),
            'stockOutLines' => MaterialStockOutLine::with(['materialStockOut.rawMaterial', 'productionLine'])->get(),
            'customers' => $this->customers=Customer::all(),
            'productions' => $this->productions,
        ]);
    }
}
