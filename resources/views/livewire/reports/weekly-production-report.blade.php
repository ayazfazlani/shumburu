<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Quality Control Weekly Production of Pipe & Raw Material Reports</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document no</span> S/P/E/PR QC:003</div>
            <div><span class="font-semibold">Week:</span> {{ $startDate }} - {{ $endDate }}</div>
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
        <div class="font-semibold underline mb-1">Comment of Quality</div>
        <div class="mb-2">In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:</div>
        <div class="font-semibold underline mb-1">Problems:</div>
        <ul class="list-disc pl-5 mb-2">
            <li>Example: 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.</li>
            <!-- Add more problems as needed -->
        </ul>
        <div class="font-semibold underline mb-1">Corrective action:</div>
        <div class="mb-2">The problems were reduced by communicating with the shift leader and operators. However, weight and raw material quality problem were not reduced because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products and shrinkage problem in 125mm products was reduced by increasing the length and OD of products and raw material problems were reduced by changing the raw materials.</div>
        <div class="font-semibold underline mb-1">Remark:</div>
        <div class="mb-2">As quality we recommended that the double type raw materials quality (purity and density) should be checked.</div>
    </div>
    <div class="mt-6 flex flex-wrap justify-between text-xs">
        <div>
            <div class="mb-1">Prepared by <span class="underline">Yohannes Choma</span></div>
            <div>Checked by <span class="underline">Yeshiamb A.</span></div>
            <div>Approved by <span class="underline">Aschalew</span></div>
        </div>
        <div class="text-right">
            <div>Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
            <div>Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
            <div>Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
        </div>
    </div>
</div> 