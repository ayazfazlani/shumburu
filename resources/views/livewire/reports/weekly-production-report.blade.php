<div>
<div class="container mx-auto py-6">
    <div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
                <div class="app-logo">SPF</div>
                <div>
                    <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                    <div class="text-xs text-gray-600">Quality Control Weekly Production of Pipe & Raw Material Reports</div>
                </div>
            </div>
            <div class="text-right text-xs">
                <div><span class="font-semibold">Document no</span> S/P/E/PR QC:003</div>
                <div><span class="font-semibold">Week:</span> {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
            <div class="flex items-center gap-2">
                <span class="font-semibold">Filters:</span>
                <input type="date" wire:model.live="startDate" class="border rounded px-2 py-1 text-xs" />
                <input type="date" wire:model.live="endDate" class="border rounded px-2 py-1 text-xs" />
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

                <button wire:click="refreshPage" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                    Apply Filters
                </button>
                <button wire:click="clearFilters" class="bg-gray-500 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">
                    Clear Filters
                </button>
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
            <span class="font-semibold">Report Period: </span>
            {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="w-full text-xs border border-collapse border-gray-400">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 p-1">Raw Material</th>
                        <th class="border border-gray-400 p-1">Qty (kg)</th>
                        <th class="border border-gray-400 p-1">Size of Pipe</th>
                        <th class="border border-gray-400 p-1">Shift</th>
                        <th class="border border-gray-400 p-1">Line</th>
                        @foreach($lengths as $length)
                            <th class="border border-gray-400 p-1 text-center">{{ $length }}m</th>
                        @endforeach
                        <th class="border border-gray-400 p-1">Total Product Weight (kg)</th>
                        <th class="border border-gray-400 p-1">Waste (kg)</th>
                        <th class="border border-gray-400 p-1">Gross (kg)</th>
                        <th class="border border-gray-400 p-1">Ovality (start-end)</th>
                        <th class="border border-gray-400 p-1">Thickness</th>
                        <th class="border border-gray-400 p-1">Outer Diameter</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalsByLength = array_fill_keys($lengths->toArray(), 0);
                        $grandRawQty = 0;
                        $grandProductWeight = 0;
                        $grandWaste = 0;
                        $grandGross = 0;
                    @endphp

                    @forelse($grouped as $productName => $rows)
                        @foreach($rows as $row)
                            @php
                                $raws = $row['raw_materials_list'] ?? [];
                                $rawCount = count($raws) ?: 1;

                                $qtyConsumed = $row['total_raw_consumed'] ?? 0;
                                $productWeight = $row['total_product_weight'] ?? 0;
                                $waste = max(0, $qtyConsumed - $productWeight);
                                $gross = $qtyConsumed;

                                $grandRawQty += $qtyConsumed;
                                $grandProductWeight += $productWeight;
                                $grandWaste += $waste;
                                $grandGross += $gross;

                                $qtyByLength = $row['qty_by_length'] ?? [];

                                $startOval = $row['avg_start_ovality'] ?? 0;
                                $endOval = $row['avg_end_ovality'] ?? 0;
                                $thicknessAvg = $row['thickness'] ?? null;
                                $outerAvg = $row['avg_outer'] ?? null;
                            @endphp

                            @foreach($raws as $index => $rm)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 p-1">{{ $rm['name'] }}</td>
                                    <td class="border border-gray-300 p-1 text-right">{{ number_format($rm['qty'], 2) }}</td>

                                    @if($index === 0)
                                        <td class="border border-gray-300 p-1" rowspan="{{ $rawCount }}">{{ $row['size'] }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $row['shift'] ?: '-' }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $row['production_line_name'] ?? $row['production_line_id'] ?? '-' }}</td>

                                        @foreach($lengths as $l)
                                            @php
                                                $qtyL = $qtyByLength[$l] ?? 0;
                                                $totalsByLength[$l] += $qtyL;
                                            @endphp
                                            <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ $qtyL ? number_format($qtyL, 2) : '' }}</td>
                                        @endforeach

                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($productWeight, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($waste, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-right" rowspan="{{ $rawCount }}">{{ number_format($gross, 2) }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ number_format($startOval, 3) }} - {{ number_format($endOval, 3) }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $thicknessAvg ? number_format($thicknessAvg, 3) : '-' }}</td>
                                        <td class="border border-gray-300 p-1 text-center" rowspan="{{ $rawCount }}">{{ $outerAvg ? number_format($outerAvg, 3) : '-' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="{{ 11 + count($lengths) }}" class="border border-gray-300 text-center py-4">No production data found for the selected filters</td>
                        </tr>
                    @endforelse

                    @if($grouped->count() > 0)
                        <tr class="font-bold bg-gray-100">
                            <td class="border border-gray-400 p-1">Total</td>
                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandRawQty, 2) }}</td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>
                            <td class="border border-gray-400 p-1"></td>

                            @foreach($lengths as $length)
                                <td class="border border-gray-400 p-1 text-right">{{ number_format($totalsByLength[$length] ?? 0, 2) }}</td>
                            @endforeach

                            <td class="border border-gray-400 p-1 text-right">{{ number_format($grandProductWeight, 2) }}</td>
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

        {{-- Quality comment area --}}
        <div class="mt-4 text-xs border border-gray-300 rounded p-3 bg-gray-50">
            @if($qualityReport)
                <div class="font-semibold underline mb-1">Comment of Quality</div>
                <div class="mb-2">{{ $qualityReport->quality_comment ?: 'In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:' }}</div>

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
                    <div class="mb-2">In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:</div>
                    <div class="font-semibold underline mb-1">Problems:</div>
                    <ul class="list-disc pl-5 mb-2">
                        <li>Example: 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.</li>
                    </ul>
                @endif
            @endif
        </div>

        @if($grouped->count() > 0)
        <div class="mt-6 flex flex-wrap justify-between text-xs">
            <div>
                <div class="mb-1">Prepared by <span class="underline">{{ $qualityReport->prepared_by ?? 'Yohannes Choma' }}</span></div>
                <div>Checked by <span class="underline">{{ $qualityReport->checked_by ?? 'Yeshiamb A.' }}</span></div>
                <div>Approved by <span class="underline">{{ $qualityReport->approved_by ?? 'Aschalew' }}</span></div>
            </div>
            <div class="text-right">
                <div>Date <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
                <div>Date <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
                <div>Date <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
            </div>
        </div>
        @endif
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