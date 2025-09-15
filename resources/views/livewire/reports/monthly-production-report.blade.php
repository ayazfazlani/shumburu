<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Quality Control Monthly Production and Raw Materials Report</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document no</span> S/P/E/PR/QC:004</div>
            <div><span class="font-semibold">Month:</span> {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Filters:</span>
            <input type="month" wire:model.live="month" class="border rounded px-2 py-1 text-xs" />
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

    <!-- Table -->
    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-black">Raw Material</th>
                    <th class="border border-black">Weight (kg)</th>
                    <th class="border border-black">Shift</th>
                    @foreach($lengths as $length)
                        <th class="border border-black">{{ $length }}m</th>
                    @endforeach
                    <th class="border border-black">Total Product Weight (kg)</th>
                    <th class="border border-black">Waste (kg)</th>
                    <th class="border border-black">Gross (kg)</th>
                    <th class="border border-black">Ovality</th>
                    <th class="border border-black">Thickness</th>
                    <th class="border border-black">Outer Diameter</th>
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

                @forelse($groupedByShift as $shift => $shiftGroup)
                    @php 
                        $shiftRowCount = 0;
                        // Calculate total rows for this shift
                        foreach($shiftGroup as $rawMaterial => $byProduct) {
                            foreach($byProduct as $productName => $bySize) {
                                $shiftRowCount += count($bySize);
                            }
                        }
                        $shiftRowIndex = 0;
                    @endphp
                    
                    @foreach($shiftGroup as $rawMaterial => $byProduct)
                        @foreach($byProduct as $productName => $bySize)
                            @foreach($bySize as $size => $records)
                                @php
                                    $shiftRowIndex++;
                                    // Raw material consumed
                                    $qtyConsumed = $records->sum(fn($rec) => $rec->materialStockOutLines->sum('quantity_consumed'));
                                    $totalQuantityConsumed += $qtyConsumed;

                                    // Product weight
                                    $productWeight = $records->sum('total_weight');
                                    if ($productWeight <= 0) {
                                        $productWeight = $records->sum('quantity') * ($records->first()->product->weight_per_meter ?? 0);
                                    }
                                    $totalProductWeight += $productWeight;

                                    // Waste
                                    $waste = max(0, $qtyConsumed - $productWeight);
                                    $totalWaste += $waste;

                                    // Gross
                                    $gross = $qtyConsumed;
                                    $totalGross += $gross;

                                    // Quality metrics
                                    $ovality = $records->avg('ovality');
                                    $thickness = $records->avg('thickness');
                                    $outerDiameter = $records->avg('outer_diameter');
                                @endphp
                                <tr>
                                    <td class="border border-black">{{ $rawMaterial }}</td>
                                    <td class="border border-black">{{ number_format($qtyConsumed, 2) }}</td>
                                    
                                    {{-- Show shift only in first row of the shift group --}}
                                    @if($shiftRowIndex === 1)
                                        <td class="border border-black align-top" rowspan="{{ $shiftRowCount }}">
                                            {{ $shift }}
                                        </td>
                                    @endif
                                    
                                    @foreach($lengths as $length)
                                        @php
                                            $qty = $records->where('length_m', $length)->sum('quantity');
                                            $totals[$length] += $qty;
                                        @endphp
                                        <td class="border border-black">{{ $qty ?: '' }}</td>
                                    @endforeach
                                    
                                    <td class="border border-black">{{ number_format($productWeight, 2) }}</td>
                                    <td class="border border-black">{{ number_format($waste, 2) }}</td>
                                    <td class="border border-black">{{ number_format($gross, 2) }}</td>
                                    <td class="border border-black">{{ $ovality ? number_format($ovality, 3) : '-' }}</td>
                                    <td class="border border-black">{{ $thickness ? number_format($thickness, 3) : '-' }}</td>
                                    <td class="border border-black">{{ $outerDiameter ? number_format($outerDiameter, 3) : '-' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="{{ 10 + count($lengths) }}" class="border border-black text-center py-4">
                            No production data found for the selected filters
                        </td>
                    </tr>
                @endforelse

                @if($groupedByShift->count() > 0)
                    <tr class="font-bold bg-gray-200">
                        <td class="border border-black">Total</td>
                        <td class="border border-black">{{ number_format($totalQuantityConsumed, 2) }}</td>
                        <td class="border border-black"></td>
                        @foreach($lengths as $length)
                            <td class="border border-black">{{ $totals[$length] }}</td>
                        @endforeach
                        <td class="border border-black">{{ number_format($totalProductWeight, 2) }}</td>
                        <td class="border border-black">{{ number_format($totalWaste, 2) }}</td>
                        <td class="border border-black">{{ number_format($totalGross, 2) }}</td>
                        <td class="border border-black">-</td>
                        <td class="border border-black">-</td>
                        <td class="border border-black">-</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Quality comments -->
    <div class="mt-4 text-xs">
        @if($qualityReport)
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">{{ $qualityReport->quality_comment ?: 'In this month all products were produced according to the standards and in a good quality...' }}</div>
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
        @endif
    </div>

    <!-- Signatures -->
    @if($groupedByShift->count() > 0)
        <div class="mt-6 flex flex-wrap justify-between text-xs">
            <div>
                <div class="mb-1">Prepared by <span class="underline">{{ $qualityReport->prepared_by ?? 'Yohannes Choma' }}</span></div>
                <div>Checked by <span class="underline">{{ $qualityReport->checked_by ?? 'Yeshiamb A.' }}</span></div>
                <div>Approved by <span class="underline">{{ $qualityReport->approved_by ?? 'Aschalew' }}</span></div>
            </div>
            <div class="text-right">
                <div>Date <span class="underline">{{ $qualityReport?->created_at?->format('d-m-Y') ?? now()->format('d-m-Y') }}</span></div>
                <div>Date <span class="underline">{{ $qualityReport?->created_at?->format('d-m-Y') ?? now()->format('d-m-Y') }}</span></div>
                <div>Date <span class="underline">{{ $qualityReport?->created_at?->format('d-m-Y') ?? now()->format('d-m-Y') }}</span></div>
            </div>
        </div>
    @endif
</div>