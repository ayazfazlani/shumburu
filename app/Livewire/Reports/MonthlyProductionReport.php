<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\FinishedGood;
use App\Models\Product;
use App\Models\MaterialStockOutLine;
use App\Models\QualityReport;

class MonthlyProductionReport extends Component
{
    public $month;
    public $shift = '';
    public $product_id = '';
    public $raw_material = '';

    public function mount()
    {
        $this->month = Carbon::now()->format('Y-m');
    }

    public function render()
    {
        $startOfMonth = Carbon::parse($this->month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($this->month . '-01')->endOfMonth();

        $finishedGoods = FinishedGood::with(['product', 'materialStockOutLines.materialStockOut.rawMaterial'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
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

        // Grouping and calculations (placeholder)
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size'
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift');
        $products = Product::select('id', 'name')->orderBy('name')->get();

        // Get quality report data for this month
        $qualityReport = QualityReport::forMonth($this->month, 'monthly')->first();

        return view('livewire.reports.monthly-production-report', [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'month' => $this->month,
            'shifts' => $shifts,
            'products' => $products,
            'qualityReport' => $qualityReport,
        ]);
    }
} 