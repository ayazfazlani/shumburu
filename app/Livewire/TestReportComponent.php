<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\Product;
use Carbon\Carbon;

class TestReportComponent extends Component
{
    public $date;
    public $shift = '';
    public $product_id = '';
    public $shifts = ['A', 'B']; // Your actual shifts
    public $products;
    public $grouped;
    public $lengths;
    public $reportData = [];

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $this->products = Product::select('id', 'name')->get();
        $this->loadReport();
    }

    public function updated($property)
    {
        // Reload report when filters change
        if (in_array($property, ['date', 'shift', 'product_id'])) {
            $this->loadReport();
        }
    }

    public function loadReport()
    {
        // Step 1: Build the base query with proper eager loading
        $query = FinishedGood::with([
            'product:id,name,weight_per_meter',
            'materialStockOutLines.materialStockOut.rawMaterial:id,name'
        ])
        ->whereDate('created_at', $this->date);

        // Step 2: Apply filters
        if ($this->product_id) {
            $query->where('product_id', $this->product_id);
        }

        // Step 3: Get the data
        $finishedGoods = $query->get();

        // Step 4: Get unique lengths for table headers
        $this->lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();

        // Step 5: Transform data for grouping (cleaner approach)
        $transformedData = $finishedGoods->map(function ($fg) {
            return $fg->materialStockOutLines->map(function ($msol) use ($fg) {
                $mso = $msol->materialStockOut;
                $rawMaterial = $mso?->rawMaterial;

                return (object) [
                    'raw_material' => $rawMaterial?->name ?? 'Unknown',
                    'quantity_consumed' => $msol?->quantity_consumed ?? 0,
                    'shift' => $msol?->shift ?? 'Unknown',
                    'product_name' => $fg->product?->name ?? 'Unknown',
                    'size' => $fg->size,
                    'length_m' => $fg->length_m,
                    'quantity' => $fg->quantity,
                    'total_weight' => $fg->total_weight,
                    'weight_per_meter' => $fg->product?->weight_per_meter ?? 0,
                    'finished_good' => $fg, // Keep reference for calculations
                ];
            });
        })->flatten();

        // Step 6: Apply shift filter after transformation
        if ($this->shift) {
            $transformedData = $transformedData->filter(function ($item) {
                return $item->shift === $this->shift;
            });
        }

        // Step 7: Group the data
        $this->grouped = $transformedData->groupBy([
            'raw_material',
            'shift', 
            'product_name',
            'size'
        ]);

        // Step 8: Prepare report data with totals
        $this->prepareReportData();
    }

    public function prepareReportData()
    {
        $this->reportData = [];
        $grandTotals = array_fill_keys($this->lengths->toArray(), 0);
        $grandTotalWeight = 0;

        foreach ($this->grouped as $rawMaterial => $byShift) {
            foreach ($byShift as $shift => $byProduct) {
                foreach ($byProduct as $productName => $bySize) {
                    foreach ($bySize as $size => $records) {
                        $row = [
                            'raw_material' => $rawMaterial,
                            'quantity_consumed' => $records->first()->quantity_consumed ?? 0,
                            'shift' => $shift,
                            'product_name' => $productName,
                            'size' => $size,
                            'length_quantities' => [],
                            'total_weight' => 0,
                            'average_weight' => 0
                        ];

                        // Calculate quantities for each length
                        foreach ($this->lengths as $length) {
                            $qty = $records->where('length_m', $length)->sum('quantity');
                            $row['length_quantities'][$length] = $qty;
                            $grandTotals[$length] += $qty;
                        }

                        // Calculate weights
                        $totalWeight = $this->calculateTotalWeight($records);
                        $row['total_weight'] = $totalWeight;
                        $row['average_weight'] = $this->calculateAverageWeight($records);
                        $grandTotalWeight += $totalWeight;

                        $this->reportData[] = $row;
                    }
                }
            }
        }

        // Add grand totals row
        $this->reportData['grand_totals'] = [
            'length_totals' => $grandTotals,
            'grand_total_weight' => $grandTotalWeight
        ];
    }

    // Helper method for weight calculations
    public function calculateTotalWeight($records)
    {
        return $records->sum(function ($record) {
            return $record->quantity * $record->length_m * $record->weight_per_meter;
        });
    }

    // Helper method for average weight
    public function calculateAverageWeight($records)
    {
        $totalWeight = $this->calculateTotalWeight($records);
        $count = $records->count();
        return $count > 0 ? $totalWeight / $count : 0;
    }

    // Export to PDF method
    public function exportToPdf()
    {
        // You can implement PDF export here
        // return response()->streamDownload(function () {
        //     echo $this->generatePdf();
        // }, 'production-report-' . $this->date . '.pdf');
    }

    // Print method
    public function printReport()
    {
        $this->dispatch('print-report');
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.test-report-component', [
            'grouped' => $this->grouped,
            'lengths' => $this->lengths,
            'products' => $this->products,
            'shifts' => $this->shifts,
            'reportData' => $this->reportData,
        ]);
    }
}