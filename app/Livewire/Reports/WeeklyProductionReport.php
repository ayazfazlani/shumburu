<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\FinishedGood;
use App\Models\Product;
use App\Models\MaterialStockOutLine;

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
        $finishedGoods = FinishedGood::with(['product', 'materialStockOutLines.materialStockOut.rawMaterial'])
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

        // Grouping and calculations (placeholder)
        $grouped = $finishedGoods->groupBy([
            fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown',
            fn($item) => $item->product->name ?? 'Unknown',
            'size'
        ]);

        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift');
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('livewire.reports.weekly-production-report', [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'shifts' => $shifts,
            'products' => $products,
        ]);
    }
} 