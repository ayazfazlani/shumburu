<?php

namespace App\Livewire\Operations;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\QualityReport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\Log;

class ProductionReport extends Component
{
    public $date;
    public $shift = '';
    public $product_id = '';
    public $raw_material = '';

    public function mount()
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function render()
    {
        $startOfDay = Carbon::parse($this->date)->startOfDay();
        $endOfDay = Carbon::parse($this->date)->endOfDay();

        $finishedGoods = FinishedGood::with([
                'product', 
                'materialStockOutLines.materialStockOut.rawMaterial',
            ])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($this->shift, function($query) {
                $query->whereHas('materialStockOutLines', function($q) {
                    $q->where('shift', $this->shift);
                });
            })
            ->when($this->product_id, function($query) {
                $query->where('product_id', $this->product_id);
            })
            ->when($this->raw_material, function($query) {
                $query->whereHas('materialStockOutLines.materialStockOut.rawMaterial', function($q) {
                    $q->where('name', $this->raw_material);
                });
            })
            ->get();

        // FIXED: Group by material, product, size, and length to ensure all records are shown
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size',
            'length_m' // Added length to the grouping
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        
        // Get shifts for filter
        $shifts = MaterialStockOutLine::select('shift')
            ->distinct()
            ->pluck('shift')
            ->filter();
            
        $products = Product::select('id', 'name')->orderBy('name')->get();
        
        // Get raw materials for filter
        $rawMaterials = FinishedGood::with('materialStockOutLines.materialStockOut.rawMaterial')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->get()
            ->pluck('materialStockOutLines')
            ->flatten()
            ->pluck('materialStockOut.rawMaterial.name')
            ->unique()
            ->filter()
            ->values();

        // Get quality report data for this date ONLY if there's production data
        $qualityReport = null;
        if ($finishedGoods->count() > 0) {
            $qualityReport = QualityReport::forDate($this->date, 'daily')->first();
        }

        return view('livewire.operations.production-report', [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'date' => $this->date,
            'shifts' => $shifts,
            'products' => $products,
            'rawMaterials' => $rawMaterials,
            'shift' => $this->shift,
            'product_id' => $this->product_id,
            'qualityReport' => $qualityReport,
        ]);
    }

    public function exportToPdf()
    {
        $startOfDay = Carbon::parse($this->date)->startOfDay();
        $endOfDay = Carbon::parse($this->date)->endOfDay();

        $finishedGoods = FinishedGood::with([
                'product', 
                'materialStockOutLines.materialStockOut.rawMaterial',
            ])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($this->shift, function($query) {
                $query->whereHas('materialStockOutLines', function($q) {
                    $q->where('shift', $this->shift);
                });
            })
            ->when($this->product_id, function($query) {
                $query->where('product_id', $this->product_id);
            })
            ->when($this->raw_material, function($query) {
                $query->whereHas('materialStockOutLines.materialStockOut.rawMaterial', function($q) {
                    $q->where('name', $this->raw_material);
                });
            })
            ->get();

        // FIXED: Use the same grouping logic as in render()
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size',
            'length_m' // Added length to the grouping
        ]);
        
        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        
        // Get quality report data for this date ONLY if there's production data
        $qualityReport = null;
        if ($finishedGoods->count() > 0) {
            $qualityReport = QualityReport::forDate($this->date, 'daily')->first();
        }

        $data = [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'date' => $this->date,
            'qualityReport' => $qualityReport,
            'startDate' => $startOfDay,
            'endDate' => $endOfDay,
        ];

        $pdf = Pdf::loadView('livewire.operations.exports.production-report', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            }, 
            "daily-production-report-{$this->date}.pdf"
        );
    }
}