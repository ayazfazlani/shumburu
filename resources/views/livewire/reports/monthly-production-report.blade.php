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
            <div><span class="font-semibold">Month:</span> {{ $month }}</div>
        </div>
    </div>
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
                @foreach($finishedGoods->pluck('materialStockOutLines')->flatten()->pluck('materialStockOut.rawMaterial.name')->unique()->filter()->values() as $rm)
                    <option value="{{ $rm }}">{{ $rm }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-black">Raw Material</th>
                    <th class="border border-black">Weight (kg)</th>
                    <th class="border border-black">Size of Pipe</th>
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
                @endphp
                @foreach($grouped as $rawMaterial => $byProduct)
                    @foreach($byProduct as $productName => $bySize)
                        @foreach($bySize as $size => $records)
                            @php
                                $qtyConsumed = $records->sum(function($rec) { return $rec->materialStockOutLines->sum('quantity_consumed'); });
                                $totalQuantityConsumed += $qtyConsumed;
                                $productWeight = $records->sum('quantity') * ($records->first()->product->weight_per_meter ?? 0);
                                $totalProductWeight += $productWeight;
                            @endphp
                            <tr>
                                <td class="border border-black">{{ $rawMaterial }}</td>
                                <td class="border border-black">{{ $qtyConsumed }}</td>
                                <td class="border border-black">{{ $size }}</td>
                                @foreach($lengths as $length)
                                    @php
                                        $qty = $records->where('length_m', $length)->sum('quantity');
                                        $totals[$length] += $qty;
                                    @endphp
                                    <td class="border border-black">{{ $qty ?: '' }}</td>
                                @endforeach
                                <td class="border border-black">{{ number_format($productWeight, 2) }}</td>
                                <td class="border border-black">0</td>
                                <td class="border border-black">0</td>
                                <td class="border border-black">-</td>
                                <td class="border border-black">-</td>
                                <td class="border border-black">-</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
                <tr class="font-bold bg-gray-200">
                    <td class="border border-black">Total</td>
                    <td class="border border-black">{{ $totalQuantityConsumed }}</td>
                    <td class="border border-black"></td>
                    @foreach($lengths as $length)
                        <td class="border border-black">{{ $totals[$length] }}</td>
                    @endforeach
                    <td class="border border-black">{{ number_format($totalProductWeight, 2) }}</td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-xs">
        @if($qualityReport)
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">{{ $qualityReport->quality_comment ?: 'In this month all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:' }}</div>
            
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
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">In this month all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:</div>
            <div class="font-semibold underline mb-1">Problems:</div>
            <ul class="list-disc pl-5 mb-2">
                <li>Example: 160mm PN10 products had a problem of weight (over from standard), thickness, high difference between maximum and minimum thickness value, internal roughness, length and fading of blue stripe, power outage.</li>
                <!-- Add more problems as needed -->
            </ul>
            <div class="font-semibold underline mb-1">Corrective action:</div>
            <div class="mb-2">Most of the problems were solved or minimized by communicating with the shift leader and operator. However, the weight problem was reduced but not eliminated because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products.</div>
            <div class="font-semibold underline mb-1">Remark:</div>
            <div class="mb-2">As quality we recommended that the double type raw materials quality (purity and density) should be checked.</div>
        @endif
    </div>
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
</div> 