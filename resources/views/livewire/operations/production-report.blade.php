
<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">

  
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Daily Quality Control Production and Raw Material Report</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document no</span> SPF/PR/QC:002</div>
            <div><span class="font-semibold">Effective date:</span> 17/06/2025</div>
        </div>
    </div>

   
    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Raw material used</span>
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
        </div>
        <div><span class="font-semibold">page 1 of 1</span></div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th rowspan="2" class="border border-black align-middle">R-M Name</th>
                     <th rowspan="2" class="border border-black align-middle">Quantity consumed</th>
                    <th rowspan="2" class="border border-black align-middle">Shift</th>
                    <th rowspan="2" class="border border-black align-middle">Size</th>
                    @foreach($lengths as $length)
                        <th class="border border-black">{{ $length }}m</th>
                    @endforeach
                    <th rowspan="2" class="border border-black align-middle">Total Weight (kg)</th>
                    <th rowspan="2" class="border border-black align-middle">Average Weight (kg/roll)</th>
                    <!-- Add more columns as needed -->
                </tr>
                <tr class="bg-gray-100">
                    @foreach($lengths as $length)
                        <th class="border border-black">{{ $length }}m</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $totals = array_fill_keys($lengths->toArray(), 0);
                    $grandTotalWeight = 0;
                @endphp
                @foreach($grouped as $rawMaterial => $byShift)
                    @foreach($byShift as $shift => $byProduct)
                        @foreach($byProduct as $productName => $bySize)
                            @foreach($bySize as $size => $records)
                                <tr>
                                    <td class="border border-black">{{ $rawMaterial }}</td>
                                    <td class="border border-black">{{ $records->first()->materialStockOutLines->first()->quantity_consumed ?? '' }}</td>
                                    <td class="border border-black">{{ $shift }}</td>
                                    <td class="border border-black">{{ $productName }}</td>
                                    <td class="border border-black">{{ $size }}</td>
                                    @foreach($lengths as $length)
                                        @php
                                            $qty = $records->where('length_m', $length)->sum('quantity');
                                            $totals[$length] += $qty;
                                        @endphp
                                        <td class="border border-black">{{ $qty > 0 ? $qty : '' }}</td>
                                    @endforeach
                                    @php
                                        $totalWeight = $records->sum(function($rec) {
                                            return $rec->quantity * $rec->length_m * ($rec->product->weight_per_meter ?? 0);
                                        });
                                        $grandTotalWeight += $totalWeight;
                                    @endphp
                                    <td class="border border-black">{{ number_format($totalWeight, 2) }}</td>
                                    <td class="border border-black">
                                        {{ $records->count() > 0 ? number_format($totalWeight / $records->count(), 2) : '' }}
                                    </td>
                                    <!-- Add more fields/calculations as needed -->
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
                <tr class="font-bold bg-gray-200">
                    <td class="border border-black">Total</td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    <td class="border border-black"></td>
                    @foreach($lengths as $length)
                        <td class="border border-black">{{ $totals[$length] }}</td>
                    @endforeach
                    <td class="border border-black">{{ number_format($grandTotalWeight, 2) }}</td>
                    <td class="border border-black"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 text-xs">
        @if($qualityReport)
            <div class="font-semibold underline mb-1">Comment of Quality</div>
            <div class="mb-2">{{ $qualityReport->quality_comment ?: 
            'Today all products were produced according to the standards and in a good quality, but we have observed some problems, corrective actions and recommended the following for the next products:-' 
            }}</div>
            
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
            <div class="mb-2">Today all products were produced according to the standards and in a good quality, but we
                have observed some problems, corrective actions and recommended the following for the next products:-</div>
            <div class="font-semibold underline mb-1">Problems:</div>
            <ul class="list-disc pl-5 mb-2">
                <li>32mm PN16 product had a problem of weight (over), thickness variation due to weight, deformation and
                    blue stripe fluctuation.</li>
                <li>125mm PN20 pipes had a problem of weight (over), thickness variation due to weight, shrinkage of outer
                    diameter and length (length=0.02m and OD=0.3mm), blue stripe fluctuation problem, crack, surface
                    roughness, power outage and OD problem.</li>
                <li>32mm PN10 product had a problem of weight over due to the standards, surface roughness, power outage,
                    breakage and blue stripe fluctuation and 25mm PN16 product had a problem of weight over due to the
                    standards, power outage, thickness problem due to weight, surface roughness and blue stripe fluctuation.
                </li>
            </ul>
            <div class="font-semibold underline mb-1">Corrective action:</div>
            <div class="mb-2">Most of the problems were reduced by communicating with the shift leader and operator.
                However, the weight problem was reduced but not eliminated because of the thickness of the products did not
                fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this
                problem we increased the weight by prioritizing the thickness of the products.</div>
            <div class="font-semibold underline mb-1">Remark:</div>
            <div class="mb-2">As quality we recommended that the double type raw materials quality (purity and density)
                should be checked.</div>
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
