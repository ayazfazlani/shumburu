<?php

namespace App\Livewire\Operations;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
// use Barryvdh\DomPDF\PDF;
use App\Models\FinishedGood;
use Livewire\WithPagination;
use App\Models\QualityReport;
use App\Models\ProductionLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\Auth;

class ProductionReport extends Component
{
    use WithPagination;

    public $date ;
    public $shift = '';
    public $product_id = '';

    public function mount()
    {
        $this->date = Carbon::today()->toDateString();
        $this->date = "2025-07-16";
    }

    public function updatingDate() { $this->resetPage(); }
    public function updatingShift() { $this->resetPage(); }
    public function updatingProductId() { $this->resetPage(); }

    // public function render()
    // {
    //     // Get all unique lengths for the selected date and filters
    //     $query = FinishedGood::query()->where('created_at', $this->date)
    //     ->when($this->shift, function($query){
          
    //         $query->whereHas('materialStockoutLine', function($q){
    //           $q->where('shift', $this->shift);
    //         });
            
        
    //     })
    //     ->when($this->product_id,function($query) {
    //         $query->where('product_id', $this->product_id);
    //     });

    //     dd($query );
    //     $lengths = $query->pluck('length_m')->unique()->sort()->values();



    //     // Fetch finished goods for the selected date and filters
    //     $finishedGoods = $query->with(['product', 'customer', 'producedBy'])->get();

    //     // Group by raw material (or product), shift, size
    //     $grouped = $finishedGoods->groupBy([
    //         fn($item) => $item->raw_material_name ?? ($item->product->name ?? 'Unknown'),
    //         'shift',
    //         'size'
    //     ]);

    //     // For filter dropdowns
    //     $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift');
    //     $products = Product::select('id', 'name')->orderBy('name')->get();

    //     return view('livewire.operations.production-report', [
    //         'lengths' => $lengths,
    //         'grouped' => $grouped,
    //         'finishedGoods' => $finishedGoods,
    //         'date' => $this->date,
    //         'shifts' => $shifts,
    //         'products' => $products,
    //         'shift' => $this->shift,
    //         'product_id' => $this->product_id,
    //     ]);
    // }

    public function render()
{
    $finishedGoods = FinishedGood::with([
        'product',
        'materialStockOutLines.materialStockOut.rawMaterial'
    ])
    
    ->whereDate('created_at', $this->date)
    ->get();

  
    $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();

    // Group by raw material name, shift, product name, and size
    $grouped = $finishedGoods->groupBy([
        fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown'
,
        fn($item) => $item->materialStockOutLines->first()->shift ?? 'Unknown',
        fn($item) => $item->product->name ?? 'Unknown',
        'size'
    ]);

    // dd($grouped);

    $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift');
    $products = Product::select('id', 'name')->orderBy('name')->get();

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
        'shift' => $this->shift,
        'product_id' => $this->product_id,
        'qualityReport' => $qualityReport,
    ]);
}

public function exportToPdf(){

    $finishedGoods = FinishedGood::with([
        'product',
        'materialStockOutLines.materialStockOut.rawMaterial'
    ])
    
    ->whereDate('created_at', $this->date)
    ->get();

  
    $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();

    // Group by raw material name, shift, product name, and size
    $grouped = $finishedGoods->groupBy([
        fn($item) => $item->materialStockOutLines->first()?->materialStockOut?->rawMaterial?->name ?? 'Unknown'
,
        fn($item) => $item->materialStockOutLines->first()->shift ?? 'Unknown',
        fn($item) => $item->product->name ?? 'Unknown',
        'size'
    ]);

    // dd($grouped);

    $shifts = MaterialStockOutLine::select('shift')->distinct()->pluck('shift');
    $products = Product::select('id', 'name')->orderBy('name')->get();

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
        'shifts' => $shifts,
        'products' => $products,
        'shift' => $this->shift,
        'product_id' => $this->product_id,
        'qualityReport' => $qualityReport,
    ];
      $pdf = Pdf::loadView('livewire.operations.exports.production-report', $data)->setPaper('a4','landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'daily-production-report.pdf');
}
}