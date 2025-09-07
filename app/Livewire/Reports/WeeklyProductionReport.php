<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\QualityReport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\Log;

class WeeklyProductionReport extends Component
{
    public $startDate;
    public $endDate;
    public $shift = '';
    public $product_id = '';
    public $raw_material = '';

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfWeek()->toDateString();
        $this->endDate = Carbon::now()->endOfWeek()->toDateString();
    }

    public function render()
    {
        $finishedGoods = FinishedGood::with([
                'product', 
                'materialStockOutLines.materialStockOut.rawMaterial'
            ])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
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

        // Grouping and calculations
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size'
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift')->filter();
        $products = Product::select('id', 'name')->orderBy('name')->get();

        // Get raw materials for filter
        $rawMaterials = FinishedGood::with('materialStockOutLines.materialStockOut.rawMaterial')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->pluck('materialStockOutLines')
            ->flatten()
            ->pluck('materialStockOut.rawMaterial.name')
            ->unique()
            ->filter()
            ->values();

        // Get quality report data for this period ONLY if there's production data
        $qualityReport = null;
        if ($finishedGoods->count() > 0) {
            $qualityReport = QualityReport::forPeriod($this->startDate, $this->endDate, 'weekly')->first();
        }

        return view('livewire.reports.weekly-production-report', [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'shifts' => $shifts,
            'products' => $products,
            'rawMaterials' => $rawMaterials,
            'qualityReport' => $qualityReport,
        ]);
    }

    public function exportToPdf()
    {
        $finishedGoods = FinishedGood::with([
                'product', 
                'materialStockOutLines.materialStockOut.rawMaterial'
            ])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
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

        // Grouping and calculations
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size'
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        
        // Get quality report data for this period ONLY if there's production data
        $qualityReport = null;
        if ($finishedGoods->count() > 0) {
            $qualityReport = QualityReport::forPeriod($this->startDate, $this->endDate, 'weekly')->first();
        }

        $data = [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'qualityReport' => $qualityReport,
        ];

        $pdf = Pdf::loadView('livewire.operations.exports.weekly-production-report', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            }, 
            "weekly-production-report-{$this->startDate}-to-{$this->endDate}.pdf"
        );
    }
}