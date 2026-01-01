<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\MaterialStockOutLine;
use App\Models\MaterialStockOut;
use App\Models\ProductionLine;
use Illuminate\Support\Facades\Auth;

class MaterialStockOutLineCrud extends Component
{
    public $lines;
    public $shift;
    public $production_line_id;
    public $materials = []; // holds multiple rows of [material_stock_out_id, quantity]

    public $materialStockOutLines = [];
    public $isEdit = false;
    public $editLineId;

    // Return functionality
    public $showReturnModal = false;
    public $returnLineId = null;
    public $returnQuantity = '';
    public $returnNotes = '';

    protected $rules = [
        'shift' => 'required|in:A,B',
        'production_line_id' => 'required|exists:production_lines,id',
        'materials.*.material_stock_out_id' => 'required|exists:material_stock_outs,id',
        'materials.*.quantity_consumed' => 'required|numeric|min:0.01',
        'returnQuantity' => 'required|numeric|min:0.01',
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
            'productionLine',
            'returnedBy'
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

    /**
     * Get available quantity for a stock out (before creating new line)
     */
    public function getAvailableQuantity($stockOutId)
    {
        return MaterialStockOutLine::getAvailableForStockOut($stockOutId);
    }

    /**
     * Validate that we're not using more than available
     */
    public function updatedMaterials($value, $key)
    {
        // Validate when material_stock_out_id or quantity_consumed changes
        if (str_contains($key, 'material_stock_out_id') || str_contains($key, 'quantity_consumed')) {
            $parts = explode('.', $key);
            $index = $parts[0];
            
            if (isset($this->materials[$index]['material_stock_out_id']) && 
                isset($this->materials[$index]['quantity_consumed'])) {
                
                $stockOutId = $this->materials[$index]['material_stock_out_id'];
                $quantity = (float) $this->materials[$index]['quantity_consumed'];
                
                if ($stockOutId && $quantity > 0) {
                    $available = $this->getAvailableQuantity($stockOutId);
                    
                    if ($quantity > $available) {
                        $this->addError("materials.{$index}.quantity_consumed", 
                            "Available quantity is only {$available}. You cannot use more than what's available.");
                    }
                }
            }
        }
    }

    public function saveBatch()
    {
        $this->validate();

        // Additional validation: Check available quantities
        foreach ($this->materials as $index => $row) {
            $available = $this->getAvailableQuantity($row['material_stock_out_id']);
            $quantity = (float) $row['quantity_consumed'];
            
            if ($quantity > $available) {
                $this->addError("materials.{$index}.quantity_consumed", 
                    "Available quantity is only {$available}. Cannot use {$quantity}.");
                return;
            }
        }

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
        session()->flash('message', 'Material stock out line created successfully.');
    }

    public function openReturnModal($lineId)
    {
        $line = MaterialStockOutLine::findOrFail($lineId);
        $this->returnLineId = $lineId;
        $this->returnQuantity = '';
        $this->returnNotes = '';
        $this->showReturnModal = true;
    }

    public function closeReturnModal()
    {
        $this->showReturnModal = false;
        $this->returnLineId = null;
        $this->returnQuantity = '';
        $this->returnNotes = '';
        $this->resetErrorBag();
    }

    public function processReturn()
    {
        $this->validate([
            'returnQuantity' => 'required|numeric|min:0.01',
            'returnNotes' => 'nullable|string|max:500',
        ]);

        $line = MaterialStockOutLine::findOrFail($this->returnLineId);
        $available = $line->available_quantity;
        $returnQty = (float) $this->returnQuantity;

        if ($returnQty > $available) {
            $this->addError('returnQuantity', 
                "You can only return up to {$available} (available unused quantity).");
            return;
        }

        // Update the line with return information
        $line->update([
            'quantity_returned' => ($line->quantity_returned ?? 0) + $returnQty,
            'return_notes' => $this->returnNotes,
            'returned_by' => Auth::id(),
            'returned_at' => now(),
        ]);

        $this->closeReturnModal();
        $this->fetch();
        session()->flash('message', "Successfully returned {$returnQty} units.");
    }

    public function delete($id)
    {
        MaterialStockOutLine::destroy($id);
        $this->fetch();
        session()->flash('message', 'Material stock out line deleted successfully.');
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
