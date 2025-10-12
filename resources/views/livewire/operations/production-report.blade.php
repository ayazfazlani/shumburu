<div>

    {{-- {{ dd($data) }} --}}
<div class="container mx-auto py-6" wire:poll(render).2s>
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
                <input type="date" wire:model.live="date" class="border rounded px-2 py-1 text-xs"  wire:change="$refresh" />
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

                <button  class="btn btn-primary btn-xs" wire:click="refreshPage"
                 wire:loading.attr="disabled" wire:target="refreshPage">Apply filters</button>
            </div>
            <div class="flex gap-2">
                {{-- <button wire:click="exportToPdf" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                    Export PDF
                </button> --}}

                <button 
    wire:click="exportToPdf" 
    wire:loading.attr="disabled"
    wire:target="exportToPdf"
    class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium 
           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:opacity-70 disabled:cursor-not-allowed"
>
    <!-- Normal State -->
    <span wire:loading.remove>
        📄 Export PDF
    </span>

    <!-- Loading State -->
    <span wire:loading class="flex items-center gap-2">
        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{-- Exporting... --}}
    </span>
</button>

            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-3 text-xs">
            <span class="font-semibold">Report Date: </span>
            {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
            <br>
            <span class="text-gray-600">Note: Length columns show quantities (pieces) for each length. Total Products column shows sum of all pieces.</span>
        </div>


        <div class="overflow-x-auto mt-2">
            <table class="w-full text-xs border border-collapse border-gray-400">
                <thead>
                    <!-- Main header row -->
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 p-1" rowspan="2">Raw Material</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Used Qty (kg)</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Size of Pipe</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Shift</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Weight/Meter</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Line</th>
                        <th class="border border-gray-400 p-1" colspan="{{ count($lengths) }}">Production Length in m/roll</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Total Products</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Total Product Weight (kg)</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Total Meters</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Waste (kg)</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Gross (kg)</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Ovality (start-end)</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Thickness</th>
                        <th class="border border-gray-400 p-1" rowspan="2">Outer Diameter</th>
                    </tr>
                    <!-- Sub header row for length columns -->
                    <tr class="bg-gray-200">
                        @foreach($lengths as $length)
                            <th class="border border-gray-400 p-1 text-center">{{ $length }}m</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalsByLength = array_fill_keys($lengths->toArray(), 0);
                        $grandRawQty = 0;
                        $grandProductWeight = 0;
                        $grandTotalMeters = 0;
                        $grandTotalProducts = 0;
                        $grandWaste = 0;
                        $grandGross = 0;
                    @endphp

                    @forelse($grouped as $productName => $rows)
                        @foreach($rows as $row)
                            @php
                                // raw_materials_list already aggregated in PHP
                                $raws = $row['raw_materials_list'] ?? [];
                                $rawCount = count($raws) ?: 1;

                                $qtyConsumed = $row['total_raw_consumed'] ?? 0;
                                $productWeight = $row['total_product_weight'] ?? 0;
                                $qtyByLength = $row['qty_by_length'] ?? [];
                                
                                // Calculate total meters: sum of (quantity * length) for all lengths
                                $totalMeters = 0;
                                foreach ($qtyByLength as $length => $qty) {
                                    $totalMeters += $qty * $length;
                                }
                                
                                // Calculate total products: sum of all quantities
                                $totalProducts = array_sum($qtyByLength);
                                
                                // Use actual recorded waste instead of calculated waste
                                $waste = $row['total_waste'] ?? 0;
                                $gross = $qtyConsumed;

                                $grandRawQty += $qtyConsumed;
                                $grandProductWeight += $productWeight;
                                $grandTotalMeters += $totalMeters;
                                $grandTotalProducts += $totalProducts;
                                $grandWaste += $waste;
                                $grandGross += $gross;

                                $ovalityAvg = null;
                                if (!empty($row['ovality_count']) && $row['ovality_count'] > 0) {
                                    $startOval = $row['start_ovality'] / max(1, $row['ovality_count']);
                                    $endOval = $row['end_ovality'] / max(1, $row['ovality_count']);
                                    $ovalityAvg = round(($startOval + $endOval) / 2, 3);
                                }
                                $thickness=  round($row['thickness']);
                                $outerAvg = ($row['outer_count'] > 0) ? round($row['outer_sum'] / $row['outer_count'], 3) : null;
                            @endphp

                            @foreach($raws as $index => $rm)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 p-1">{{ $rm['name'] }}</td>
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($rm['qty'], 2) }}</td>

                                    @if($index === 0)
                                        <td class="border border-gray-300 p-1" rowspan="{{ $rawCount }}">{{ $row['size'] }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $row['shift'] ?: '-' }}</td>
                                         <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $row['weight_per_meter'] ?: '-' }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $row['production_line_name'] ?? $row['production_line_id'] ?? '-' }}</td>

                                        @foreach($lengths as $l)
                                            @php
                                                $qtyL = $qtyByLength[$l] ?? 0;
                                                $totalsByLength[$l] += $qtyL;
                                            @endphp
                                            <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ $qtyL ? number_format($qtyL, 2) : '' }}</td>
                                        @endforeach

                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($totalProducts, 0) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($productWeight, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($totalMeters, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($waste, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($gross, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $startOval }}={{$endOval}}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $thickness ?? '-' }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $outerAvg ?? '-' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="{{ 13 + count($lengths) }}" class="border border-gray-300 text-center py-4">No production data found for the selected filters</td>
                        </tr>
                    @endforelse

                    @if($grouped->count() > 0)
                        <tr class="font-bold bg-gray-100">
                            <td class="border border-gray-400 p-1">Total</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandRawQty, 2) }}</td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>

                            @foreach($lengths as $length)
                                <td class="border border-gray-400 p-1 text-right">{{ number_format($totalsByLength[$length] ?? 0, 2) }}</td>
                            @endforeach

                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandTotalProducts, 0) }}</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandProductWeight, 2) }}</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandTotalMeters, 2) }}</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandWaste, 2) }}</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandGross, 2) }}</td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Quality comment area (same style) --}}
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
                @endif
            @endif
        </div>
    </div>
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
</style>
</div>

@push('scripts')
<script>
    window.addEventListener('refresh-page', event => {
        window.location.reload();
    })
</script>
@endpush