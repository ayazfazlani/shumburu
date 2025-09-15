<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\QualityReport;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $endOfMonth   = Carbon::parse($this->month . '-01')->endOfMonth();

        // ✅ Query with filters
        $finishedGoods = FinishedGood::with([
                'product',
                'materialStockOutLines.materialStockOut.rawMaterial',
            ])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->when($this->shift, fn($q) => $q->whereHas('materialStockOutLines', fn($sq) => $sq->where('shift', $this->shift)))
            ->when($this->product_id, fn($q) => $q->where('product_id', $this->product_id))
            ->when($this->raw_material, fn($q) => $q->whereHas('materialStockOutLines.materialStockOut.rawMaterial', fn($sq) => $sq->where('name', $this->raw_material)))
            ->get();

        // ✅ Grouped by Shift → Raw Material → Product → Size
        $groupedByShift = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->shift ?? 'N/A',
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size',
        ]);

        // ✅ Build dropdowns from filtered results
        $lengths     = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        $shifts      = $finishedGoods->pluck('materialStockOutLines')->flatten()->pluck('shift')->unique()->filter()->values();
        $products    = $finishedGoods->pluck('product')->unique('id')->values();
        $rawMaterials= $finishedGoods->pluck('materialStockOutLines')->flatten()->pluck('materialStockOut.rawMaterial.name')->unique()->filter()->values();

        // ✅ Totals for filtered dataset
        $filteredTotals = [
            'quantity_consumed' => $finishedGoods->sum(fn($fg) => $fg->materialStockOutLines->sum('quantity_consumed')),
            'product_weight'    => $finishedGoods->sum(fn($fg) => $fg->total_weight > 0 ? $fg->total_weight : $fg->quantity * ($fg->product->weight_per_meter ?? 0)),
        ];
        $filteredTotals['waste'] = max(0, $filteredTotals['quantity_consumed'] - $filteredTotals['product_weight']);
        $filteredTotals['gross'] = $filteredTotals['quantity_consumed'];

        // ✅ Monthly totals (ignore filters)
        $monthlyGoods = FinishedGood::with(['product','materialStockOutLines.materialStockOut.rawMaterial'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $monthlyTotals = [
            'quantity_consumed' => $monthlyGoods->sum(fn($fg) => $fg->materialStockOutLines->sum('quantity_consumed')),
            'product_weight'    => $monthlyGoods->sum(fn($fg) => $fg->total_weight > 0 ? $fg->total_weight : $fg->quantity * ($fg->product->weight_per_meter ?? 0)),
        ];
        $monthlyTotals['waste'] = max(0, $monthlyTotals['quantity_consumed'] - $monthlyTotals['product_weight']);
        $monthlyTotals['gross'] = $monthlyTotals['quantity_consumed'];

        $qualityReport = $finishedGoods->count() > 0
            ? QualityReport::forMonth($this->month, 'monthly')->first()
            : null;

        return view('livewire.reports.monthly-production-report', [
            'lengths'         => $lengths,
            'groupedByShift'  => $groupedByShift,
            'finishedGoods'   => $finishedGoods,
            'month'           => $this->month,
            'shifts'          => $shifts,
            'products'        => $products,
            'rawMaterials'    => $rawMaterials,
            'qualityReport'   => $qualityReport,
            'filteredTotals'  => $filteredTotals,
            'monthlyTotals'   => $monthlyTotals,
        ]);
    }

    public function exportToPdf()
    {
        $startOfMonth = Carbon::parse($this->month . '-01')->startOfMonth();
        $endOfMonth   = Carbon::parse($this->month . '-01')->endOfMonth();

        $finishedGoods = FinishedGood::with([
                'product',
                'materialStockOutLines.materialStockOut.rawMaterial',
            ])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size'
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();

        $qualityReport = $finishedGoods->count() > 0
            ? QualityReport::forMonth($this->month, 'monthly')->first()
            : null;

        $data = [
            'lengths'        => $lengths,
            'grouped'        => $grouped,
            'finishedGoods'  => $finishedGoods,
            'month'          => $this->month,
            'qualityReport'  => $qualityReport,
        ];

        $pdf = Pdf::loadView('livewire.operations.exports.monthly-production-report', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "monthly-production-report-{$this->month}.pdf"
        );
    }
}
