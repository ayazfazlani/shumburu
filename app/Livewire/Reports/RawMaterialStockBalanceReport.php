<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\RawMaterial;
use App\Models\MaterialStockIn;
use App\Models\MaterialStockOut;
use App\Models\ScrapWaste;

class RawMaterialStockBalanceReport extends Component
{
    public $date;
    public $raw_material_id = '';

    public function mount()
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function render()
    {
        $date = $this->date;
        $materialsQuery = RawMaterial::orderBy('name');
        if ($this->raw_material_id) {
            $materialsQuery->where('id', $this->raw_material_id);
        }
        $materials = $materialsQuery->get();
        $allMaterials = RawMaterial::orderBy('name')->get();
        $rows = [];
        foreach ($materials as $material) {
            // Get current stock from raw_materials table
            $currentStock = $material->quantity;
            
            // Calculate stock in for the selected date
            $addition = $material->stockIns()
                ->where('received_date', $date)
                ->sum('quantity');
            
            // Calculate stock out for the selected date
            $out = $material->stockOuts()
                ->where('issued_date', $date)
                ->sum('quantity');
            
            // Calculate beginning balance (current stock + out - addition)
            $beginning = $currentStock + $out - $addition;
            
            // Ending balance is current stock
            $ending = $currentStock;
            
            // Return is always 0 for now
            $return = 0;
            
            $rows[] = [
                'name' => $material->name,
                'beginning' => $beginning,
                'addition' => $addition,
                'out' => $out,
                'return' => $return,
                'ending' => $ending,
                'remark' => '',
            ];
        }
        return view('livewire.reports.raw-material-stock-balance-report', [
            'rows' => $rows,
            'date' => $this->date,
            'allMaterials' => $allMaterials,
            'raw_material_id' => $this->raw_material_id,
        ]);
    }
} 