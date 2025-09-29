<?php

namespace App\Livewire\Operations;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\FinishedGood;
use App\Models\QualityReport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MaterialStockOutLine;
use App\Models\RawMaterial;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class ProductionReport extends Component
{
    use WithPagination;

    public $date;
    public $shift = '';
    public $product_id = '';
    public $raw_material = '';

    // ✅ Enable query string to maintain filter state in URL
    protected $queryString = [
        'date' => ['except' => '', 'as' => 'd'],
        'shift' => ['except' => '', 'as' => 's'],
        'product_id' => ['except' => '', 'as' => 'p'],
        'raw_material' => ['except' => '', 'as' => 'rm'],
    ];


   
    public function mount()
    {
        // Initialize from query string parameters or use defaults
        $this->date = request()->query('d', Carbon::today()->toDateString());
        $this->shift = request()->query('s', '');
        $this->product_id = request()->query('p', '');
        $this->raw_material = request()->query('rm', '');
    }

    
    // ✅ Apply filters and reload the page with URL parameters
    public function applyFilters()
    {
        // This will reset the pagination and trigger a component re-render
        $this->resetPage();
    }

    // ✅ Clear all filters and reload
    public function clearFilters()
    {
        $this->reset(['shift', 'product_id', 'raw_material']);
        $this->resetPage();
        
        // Force a page reload to update URL
        $this->dispatch('filters-cleared');
    }

    // ✅ Reset to today's date
    public function resetDate()
    {
        $this->date = Carbon::today()->toDateString();
        $this->resetPage();
    }

    // ✅ Updated method to apply filters immediately and trigger a component re-render
    // Note: Livewire will re-render the component, not the entire page. This is the intended behavior.
    public function updated($property)
    {
        if (in_array($property, ['date', 'shift', 'product_id', 'raw_material'])) {
            // This will reset the pagination and re-render the component,
            // effectively reloading the data based on the new filter.
            $this->applyFilters();
        }
    }

    // This method dispatches a browser event to trigger a full page reload
    public function forceFullReload()
    {
        $this->dispatch('force-full-reload');
    }

    private function buildMergedGroups($finishedGoods): Collection
    {
        $merged = [];

        foreach ($finishedGoods as $fg) {
            $productName = $fg->product->name ?? 'Unknown';
            $size = $fg->product->name ?? 'Unknown';
            $wieghtPrMeter = $fg->product->weight_per_meter ?? 0;
            $length = $fg->length_m ?? 0;
            $fgQty = (float) ($fg->quantity ?? 0);
            $fgWeight = (float) ($fg->total_weight ?? 0);
            $startOval = $fg->start_ovality ?? null;
            $endOval = $fg->end_ovality ?? null;
            $thickness = $fg->thickness ?? null;
            $outer = $fg->outer_diameter ?? null;

            foreach ($fg->materialStockOutLines as $line) {
                $rmName = $line->materialStockOut->rawMaterial->name ?? 'Unknown';
                $rmQty = (float) ($line->quantity_consumed ?? 0);
                $lineShift = $line->shift ?? ($line->materialStockOut->shift ?? '');
                $prodLineId = $line->production_line_id ?? ($line->productionLine->id ?? 'no-line');
                $prodLineName = $line->productionLine->name ?? ('Line ' . $prodLineId);

                $key = implode('|', [
                    $productName,
                    $size,
                    $length,
                    $lineShift,
                    $prodLineId
                ]);

                if (!isset($merged[$key])) {
                    $merged[$key] = [
                        'product' => $productName,
                        'weight_per_meter' => $wieghtPrMeter,
                        'size' => $size,
                        'length' => $length,
                        'shift' => $lineShift,
                        'production_line_id' => $prodLineId,
                        'production_line_name' => $prodLineName,
                        'raw_materials' => [],
                        'total_raw_consumed' => 0.0,
                        'total_product_weight' => 0.0,
                        'total_product_qty' => 0.0,
                        'qty_by_length' => [],
                        'start_ovality' => 0.0,
                        'end_ovality' => 0.0,
                        'ovality_count' => 0,
                        'thickness' => $thickness,
                        'thickness_count' => 0,
                        'outer_sum' => 0.0,
                        'outer_count' => 0,
                    ];
                }

                $merged[$key]['raw_materials'][$rmName] =
                    ($merged[$key]['raw_materials'][$rmName] ?? 0.0) + $rmQty;
                $merged[$key]['total_raw_consumed'] += $rmQty;

                $merged[$key]['total_product_weight'] += $fgWeight;
                $merged[$key]['total_product_qty'] += $fgQty;

                $lenKey = $length;
                $merged[$key]['qty_by_length'][$lenKey] =
                    ($merged[$key]['qty_by_length'][$lenKey] ?? 0.0) + $fgQty;

                if (!is_null($startOval)) {
                    $merged[$key]['start_ovality'] += (float) $startOval;
                    $merged[$key]['ovality_count']++;
                }
                if (!is_null($endOval)) {
                    $merged[$key]['end_ovality'] += (float) $endOval;
                    if ($startOval === null) {
                        $merged[$key]['ovality_count']++;
                    }
                }
                // if (!is_null($thickness)) {
                //     $merged[$key]['thickness_sum'] += (float) $thickness;
                //     $merged[$key]['thickness_count']++;
                // }
                if (!is_null($outer)) {
                    $merged[$key]['outer_sum'] += (float) $outer;
                    $merged[$key]['outer_count']++;
                }
            }
        }

        $prepared = collect($merged)->map(function ($item) {
            $materials = [];
            foreach ($item['raw_materials'] as $name => $qty) {
                $materials[] = ['name' => $name, 'qty' => (float) $qty];
            }
            usort($materials, fn($a, $b) => strcmp($a['name'], $b['name']));
            $item['raw_materials_list'] = $materials;
            
            // Calculate averages
            $item['avg_start_ovality'] = $item['ovality_count'] > 0 ? $item['start_ovality'] / $item['ovality_count'] : 0;
            $item['avg_end_ovality'] = $item['ovality_count'] > 0 ? $item['end_ovality'] / $item['ovality_count'] : 0;
            // $item['avg_thickness'] = $item['thickness_count'] > 0 ? $item['thickness_sum'] / $item['thickness_count'] : 0;
            $item['avg_outer'] = $item['outer_count'] > 0 ? $item['outer_sum'] / $item['outer_count'] : 0;
            
            return $item;
        });

        return $prepared->mapToGroups(fn($item) => [$item['product'] => $item]);
    }

    public function render()
    {
        $startOfDay = Carbon::parse($this->date)->startOfDay();
        $endOfDay = Carbon::parse($this->date)->endOfDay();

        // Main query with pagination
        $finishedGoodsQuery = FinishedGood::with([
                'product',
                'materialStockOutLines.materialStockOut.rawMaterial',
                'materialStockOutLines.productionLine'
            ])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($this->shift, function($q) {
                return $q->whereHas('materialStockOutLines', function($qq) {
                    $qq->where('shift', $this->shift);
                });
            })
            ->when($this->product_id, function($q) {
                return $q->where('product_id', $this->product_id);
            })
            ->when($this->raw_material, function($q) {
                return $q->whereHas('materialStockOutLines.materialStockOut.rawMaterial', function($qq) {
                    $qq->where('name', $this->raw_material);
                });
            })
            ->orderBy('created_at', 'desc');

        // Get paginated results for display
        $paginatedFinishedGoods = $finishedGoodsQuery->paginate(50);
        
        // Get all results for grouping calculations
        $allFinishedGoods = $finishedGoodsQuery->get();

        $grouped = $this->buildMergedGroups($allFinishedGoods);
        $lengths = $allFinishedGoods->pluck('length_m')->unique()->sort()->values();
        $shifts = MaterialStockOutLine::select('shift')->distinct()->whereNotNull('shift')->pluck('shift')->filter();
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $rawMaterials = RawMaterial::select('name')->orderBy('name')->pluck('name')->filter()->values();

        $qualityReport = $allFinishedGoods->count() > 0
            ? QualityReport::forDate($this->date, 'daily')->first()
            : null;

        return view('livewire.operations.production-report', [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $paginatedFinishedGoods,
            'allFinishedGoods' => $allFinishedGoods,
            'date' => $this->date,
            'shifts' => $shifts,
            'products' => $products,
            'rawMaterials' => $rawMaterials,
            'shift' => $this->shift,
            'product_id' => $this->product_id,
            'raw_material' => $this->raw_material,
            'qualityReport' => $qualityReport,
            'totalRecords' => $allFinishedGoods->count(),
            'currentPageRecords' => $paginatedFinishedGoods->count(),
        ]);
    }

    public function reload(){
        $this->dispatchBrowserEvent('refresh-page');
    }

       public function refreshPage()
    {
        return redirect(request()->header('referer'));
        // Alternatively, you can use:
        // return redirect(request()->url());
    }
    public function exportToPdf()
    {
        $startOfDay = Carbon::parse($this->date)->startOfDay();
        $endOfDay = Carbon::parse($this->date)->endOfDay();

        $finishedGoods = FinishedGood::with([
                'product',
                'materialStockOutLines.materialStockOut.rawMaterial',
                'materialStockOutLines.productionLine'
            ])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($this->shift, function($q) {
                return $q->whereHas('materialStockOutLines', function($qq) {
                    $qq->where('shift', $this->shift);
                });
            })
            ->when($this->product_id, function($q) {
                return $q->where('product_id', $this->product_id);
            })
            ->when($this->raw_material, function($q) {
                return $q->whereHas('materialStockOutLines.materialStockOut.rawMaterial', function($qq) {
                    $qq->where('name', $this->raw_material);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $this->buildMergedGroups($finishedGoods);
        $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
        $qualityReport = $finishedGoods->count() > 0
            ? QualityReport::forDate($this->date, 'daily')->first()
            : null;

        $data = [
            'lengths' => $lengths,
            'grouped' => $grouped,
            'finishedGoods' => $finishedGoods,
            'date' => $this->date,
            'shift' => $this->shift,
            'product_id' => $this->product_id,
            'raw_material' => $this->raw_material,
            'qualityReport' => $qualityReport,
            'filters' => [
                'shift' => $this->shift,
                'product' => Product::find($this->product_id)->name ?? 'All',
                'raw_material' => $this->raw_material ?: 'All',
            ]
        ];

        $pdf = Pdf::loadView('livewire.operations.exports.production-report', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "daily-production-report-{$this->date}.pdf"
        );
    }
}
