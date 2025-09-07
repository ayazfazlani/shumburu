<div>
    <div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <div class="app-logo">SPF</div>
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs text-gray-600">Quality Control Daily Production of Pipe & Raw Material Reports</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document no</span> S/P/E/PR QC:001</div>
            <div><span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
        </div>
    </div>
    
    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Filters:</span>
            <input type="date" wire:model.live="date" class="border rounded px-2 py-1 text-xs" />
            <select wire:model.live="shift" class="border rounded px-2 py-1 text-xs">
                <option value="">All Shifts</option>
                @foreach($shifts as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
            <select wire:model.live="product_id" class="border rounded px-2 py-1 text-xs">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="raw_material" class="border rounded px-2 py-1 text-xs">
                <option value="">All Raw Materials</option>
                @foreach($rawMaterials as $rm)
                    <option value="{{ $rm }}">{{ $rm }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportToPdf" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                Export PDF
            </button>
        </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-3 text-xs">
        <span class="font-semibold">Report Date: </span>
        {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
    </div>

    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-gray-400">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-400 p-1">Raw Material</th>
                    <th class="border border-gray-400 p-1">Weight (kg)</th>
                    <th class="border border-gray-400 p-1">Size of Pipe</th>
                    @foreach($lengths as $length)
                        <th class="border border-gray-400 p-1 text-center">{{ $length }}m</th>
                    @endforeach
                    <th class="border border-gray-400 p-1">Total Product Weight (kg)</th>
                    <th class="border border-gray-400 p-1">Waste (kg)</th>
                    <th class="border border-gray-400 p-1">Gross (kg)</th>
                    <th class="border border-gray-400 p-1">Ovality</th>
                    <th class="border border-gray-400 p-1">Thickness</th>
                    <th class="border border-gray-400 p-1">Outer Diameter</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totals = array_fill_keys($lengths->toArray(), 0);
                    $totalQuantityConsumed = 0;
                    $totalProductWeight = 0;
                    $totalWaste = 0;
                    $totalGross = 0;
                @endphp
                
                @forelse($grouped as $rawMaterial => $byProduct)
                    @foreach($byProduct as $productName => $bySize)
                        @foreach($bySize as $size => $byLength)
                            @foreach($byLength as $length => $records)
                                @php
                                    // Raw material consumed
                                    $qtyConsumed = $records->sum(function($rec) { 
                                        return $rec->materialStockOutLines->sum('quantity_consumed'); 
                                    });
                                    $totalQuantityConsumed += $qtyConsumed;
                                    
                                    // Use the actual total_weight field from database if available, otherwise calculate
                                    $productWeight = $records->sum('total_weight');
                                    if ($productWeight <= 0) {
                                        // Fallback calculation if total_weight is not set
                                        $productWeight = $records->sum('quantity') * ($records->first()->product->weight_per_meter ?? 0);
                                    }
                                    $totalProductWeight += $productWeight;
                                    
                                    // Calculate waste
                                    $waste = max(0, $qtyConsumed - $productWeight);
                                    $totalWaste += $waste;
                                    
                                    // Gross weight
                                    $gross = $qtyConsumed;
                                    $totalGross += $gross;
                                    
                                    // Get quality metrics - use average values for the group
                                    $ovality = $records->avg('ovality');
                                    $thickness = $records->avg('thickness');
                                    $outerDiameter = $records->avg('outer_diameter');
                                    
                                    // Get the actual size from the first record if size is empty
                                    $displaySize = $size ?: ($records->first()->size ?? 'N/A');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 p-1">{{ $rawMaterial }}</td>
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($qtyConsumed, 2) }}</td>
                                    <td class="border border-gray-300 p-1">{{ $displaySize }}</td>
                                    @foreach($lengths as $l)
                                        @php
                                            $qty = ($l == $length) ? $records->sum('quantity') : 0;
                                            $totals[$l] += $qty;
                                        @endphp
                                        <td class="border border-gray-300 p-1 text-right">{{ $qty ?: '' }}</td>
                                    @endforeach
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($productWeight, 2) }}</td>
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($waste, 2) }}</td>
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($gross, 2) }}</td>
                                    <td class="border border-gray-300 p-1 text-center">{{ $ovality ? number_format($ovality, 3) : '-' }}</td>
                                    <td class="border border-gray-300 p-1 text-center">{{ $thickness ? number_format($thickness, 3) : '-' }}</td>
                                    <td class="border border-gray-300 p-1 text-center">{{ $outerDiameter ? number_format($outerDiameter, 3) : '-' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="{{ 10 + count($lengths) }}" class="border border-gray-300 text-center py-4">No production data found for the selected filters</td>
                    </tr>
                @endforelse
                
                @if($grouped->count() > 0)
                <tr class="font-bold bg-gray-100">
                    <td class="border border-gray-400 p-1">Total</td>
                    <td class="border border-gray-400 p-1 text-right">{{ number_format($totalQuantityConsumed, 2) }}</td>
                    <td class="border border-gray-400 p-1"></td>
                    @foreach($lengths as $length)
                        <td class="border border-gray-400 p-1 text-right">{{ $totals[$length] }}</td>
                    @endforeach
                    <td class="border border-gray-400 p-1 text-right">{{ number_format($totalProductWeight, 2) }}</td>
                    <td class="border border-gray-400 p-1 text-right">{{ number_format($totalWaste, 2) }}</td>
                    <td class="border border-gray-400 p-1 text-right">{{ number_format($totalGross, 2) }}</td>
                    <td class="border border-gray-400 p-1"></td>
                    <td class="border border-gray-400 p-1"></td>
                    <td class="border border-gray-400 p-1"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 text-xs border border-gray-300 rounded p-3 bg-gray-50">
        @if($qualityReport)
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">{{ $qualityReport->quality_comment ?: 'Today all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following:' }}</div>
            
            @if($qualityReport->problems)
                <div class="font-semibold underline mb-1">Problems:</div>
                <div class="mb-2">{!! nl2br(e($qualityReport->problems)) !!}</div>
            @endif
            
            @if($qualityReport->corrective_actions)
                <div class="font-semibold underline mb-1">Corrective action:</div>
                <div class="mb-2">{!! nl2br(e($qualityReport->corrective_actions)) !!}</div>
            @endif
            
            @if($qualityReport->remarks)
                <div class="font-semibold underline mb-1">Remark:</div>
                <div class="mb-2">{!! nl2br(e($qualityReport->remarks)) !!}</div>
            @endif
        @else
            @if($grouped->count() > 0)
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">Today all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following:</div>
            
            <div class="font-semibold underline mb-1">Problems:</div>
            <ul class="list-disc pl-5 mb-2">
                <li>Example: 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.</li>
            </ul>
            
            <div class="font-semibold underline mb-1">Corrective action:</div>
            <div class="mb-2">The problems were reduced by communicating with the shift leader and operators. However, weight and raw material quality problem were not reduced because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products and shrinkage problem in 125mm products was reduced by increasing the length and OD of products and raw material problems were reduced by changing the raw materials.</div>
            
            <div class="font-semibold underline mb-1">Remark:</div>
            <div class="mb-2">As quality we recommended that the double type raw materials quality (purity and density) should be checked.</div>
            @endif
        @endif
    </div>
    
    @if($grouped->count() > 0)
    <div class="mt-6 flex flex-wrap justify-between text-xs">
        <div class="space-y-4">
            <div class="signature-item">
                <div class="mb-1">Prepared by:</div>
                <div class="underline" style="min-width: 150px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport->prepared_by ?? 'Yohannes Choma' }}</div>
            </div>
            
            <div class="signature-item">
                <div class="mb-1">Checked by:</div>
                <div class="underline" style="min-width: 150px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport->checked_by ?? 'Yeshiamb A.' }}</div>
            </div>
            
            <div class="signature-item">
                <div class="mb-1">Approved by:</div>
                <div class="underline" style="min-width: 150px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport->approved_by ?? 'Aschalew' }}</div>
            </div>
        </div>
        
        <div class="space-y-4 text-right">
            <div class="signature-item">
                <div class="mb-1">Date:</div>
                <div class="underline" style="min-width: 100px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
            </div>
            
            <div class="signature-item">
                <div class="mb-1">Date:</div>
                <div class="underline" style="min-width: 100px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
            </div>
            
            <div class="signature-item">
                <div class="mb-1">Date:</div>
                <div class="underline" style="min-width: 100px; height: 20px;"></div>
                <div class="text-xs text-gray-500 mt-1">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .app-logo {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    }
    
    .signature-item {
        display: flex;
        flex-direction: column;
    }
    
    .underline {
        border-bottom: 1px solid #4b5563;
        margin-top: 4px;
    }
    
    table {
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #d1d5db;
        padding: 4px 8px;
    }
    
    thead th {
        background-color: #f3f4f6;
        font-weight: 600;
    }
    
    tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    tbody tr:hover {
        background-color: #f1f5f9;
    }
</style>
</div>