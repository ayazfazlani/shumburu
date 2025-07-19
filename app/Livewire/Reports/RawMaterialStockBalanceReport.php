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
            // Beginning balance: latest stock in before or on the date
            $beginning = $material->stockIns()
                ->where('received_date', '<=', $date)
                ->orderByDesc('received_date')
                ->value('quantity') ?? 0;
            // Ending balance: latest stock out before or on the date
            $ending = $material->stockOuts()
                ->where('issued_date', '<=', $date)
                ->orderByDesc('issued_date')
                ->value('quantity') ?? 0;
            // Addition, OUT, Return: set to 0 for now
            $addition = 0;
            $out = 0;
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