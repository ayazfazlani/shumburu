<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Daily Raw Material Stock Balance</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document No.</span> PR/OF/012</div>
            <div><span class="font-semibold">Effective Date:</span> {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Filters:</span>
            <input type="date" wire:model.live="date" class="border rounded px-2 py-1 text-xs" />
            <select wire:model.live="raw_material_id" class="border rounded px-2 py-1 text-xs">
                <option value="">All Raw Materials</option>
                @foreach($allMaterials as $material)
                <option value="{{ $material->id }}">{{ $material->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <span class="font-semibold">Total Materials:</span>
            <span class="bg-gray-100 px-2 py-1 rounded">{{ count($rows) }}</span>
        </div>
    </div>

    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-black p-1">S/NO</th>
                    <th class="border border-black p-1">Raw material name</th>
                    <th class="border border-black p-1">Beginning balance (kg)</th>
                    <th class="border border-black p-1">Addition (kg)</th>
                    <th class="border border-black p-1">OUT (kg)</th>
                    <th class="border border-black p-1">Return (kg)</th>
                    <th class="border border-black p-1">Waste (kg)</th>
                    <th class="border border-black p-1">Ending Balance (kg)</th>
                    <th class="border border-black p-1">Status</th>
                    <th class="border border-black p-1">Remark</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalBeginning = 0;
                $totalAddition = 0;
                $totalOut = 0;
                $totalReturn = 0;
                $totalWaste = 0;
                $totalEnding = 0;
                @endphp

                @foreach($rows as $i => $row)
                @php
                $totalBeginning += $row['beginning'];
                $totalAddition += $row['addition'];
                $totalOut += $row['out'];
                $totalReturn += $row['return'];
                $totalWaste += $row['waste'];
                $totalEnding += $row['ending'];
                @endphp
                <tr class="{{ $row['ending'] < 0 ? 'bg-red-50' : '' }}">
                    <td class="border border-black p-1 text-center">{{ $i + 1 }}</td>
                    <td class="border border-black p-1 font-medium">{{ $row['name'] }}</td>
                    <td class="border border-black p-1 text-right">{{ number_format($row['beginning'], 2) }}</td>
                    <td class="border border-black p-1 text-right text-green-600">+{{ number_format($row['addition'], 2)
                        }}</td>
                    <td class="border border-black p-1 text-right text-red-600">-{{ number_format($row['out'], 2) }}
                    </td>
                    <td class="border border-black p-1 text-right text-blue-600">+{{ number_format($row['return'], 2) }}
                    </td>
                    <td class="border border-black p-1 text-right text-orange-600">-{{ number_format($row['waste'], 2)
                        }}</td>
                    <td class="border border-black p-1 text-right font-semibold 
                            {{ $row['ending'] < 0 ? 'text-red-600' : 'text-black' }}">
                        {{ number_format($row['ending'], 2) }}
                    </td>
                    <td class="border border-black p-1 text-center">
                        @if($row['ending'] < 0) <span class="text-red-600 font-semibold">SHORTAGE</span>
                            @elseif($row['ending'] < $row['min_stock']) <span class="text-orange-600 font-semibold">LOW
                                STOCK</span>
                                @else
                                <span class="text-green-600">OK</span>
                                @endif
                    </td>
                    <td
                        class="border border-black p-1 text-xs 
                            {{ str_contains($row['remark'], 'discrepancy') ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        {{ $row['remark'] }}
                    </td>
                </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="bg-gray-100 font-semibold">
                    <td class="border border-black p-1 text-center" colspan="2">TOTAL</td>
                    <td class="border border-black p-1 text-right">{{ number_format($totalBeginning, 2) }}</td>
                    <td class="border border-black p-1 text-right text-green-600">+{{ number_format($totalAddition, 2)
                        }}</td>
                    <td class="border border-black p-1 text-right text-red-600">-{{ number_format($totalOut, 2) }}</td>
                    <td class="border border-black p-1 text-right text-blue-600">+{{ number_format($totalReturn, 2) }}
                    </td>
                    <td class="border border-black p-1 text-right text-orange-600">-{{ number_format($totalWaste, 2) }}
                    </td>
                    <td class="border border-black p-1 text-right">{{ number_format($totalEnding, 2) }}</td>
                    <td class="border border-black p-1 text-center">
                        @if($totalEnding < 0) <span class="text-red-600">CRITICAL</span>
                            @else
                            <span class="text-green-600">BALANCED</span>
                            @endif
                    </td>
                    <td class="border border-black p-1"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="bg-green-50 p-2 rounded border">
            <div class="font-semibold">Total Addition</div>
            <div class="text-green-600 font-bold">{{ number_format($totalAddition, 2) }} kg</div>
        </div>
        <div class="bg-red-50 p-2 rounded border">
            <div class="font-semibold">Total Consumption</div>
            <div class="text-red-600 font-bold">{{ number_format($totalOut, 2) }} kg</div>
        </div>
        <div class="bg-blue-50 p-2 rounded border">
            <div class="font-semibold">Total Returns</div>
            <div class="text-blue-600 font-bold">{{ number_format($totalReturn, 2) }} kg</div>
        </div>
        <div class="bg-orange-50 p-2 rounded border">
            <div class="font-semibold">Total Waste</div>
            <div class="text-orange-600 font-bold">{{ number_format($totalWaste, 2) }} kg</div>
        </div>
    </div>

    <!-- Remarks Section -->
    <div class="mt-4 text-xs border-t pt-3">
        <div class="font-semibold mb-2">Production Notes:</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-gray-700">
            <div>• 65% scrap 140*30=4200kg send to ferida</div>
            <div>• 440kg is D=20% & V=60% & 65%pv=20%</div>
            <div>• Material efficiency: {{ $totalOut > 0 ? number_format((($totalOut - $totalWaste) / $totalOut) * 100,
                1) : 0 }}%</div>
            <div>• Return rate: {{ $totalOut > 0 ? number_format(($totalReturn / $totalOut) * 100, 1) : 0 }}%</div>
        </div>
    </div>

    <!-- Approval Section -->
    <div class="mt-6 flex flex-wrap justify-between text-xs border-t pt-4">
        <div class="space-y-4">
            <div>
                <div class="mb-1 font-semibold">Prepared by</div>
                <div class="border-b border-black w-32 inline-block">Hana</div>
                <div class="text-gray-500 text-xs mt-1">Signature & Date</div>
            </div>
            <div>
                <div class="mb-1 font-semibold">Checked by</div>
                <div class="border-b border-black w-32 inline-block">Minch</div>
                <div class="text-gray-500 text-xs mt-1">Signature & Date</div>
            </div>
            <div>
                <div class="mb-1 font-semibold">Approved by</div>
                <div class="border-b border-black w-32 inline-block">Minch A.</div>
                <div class="text-gray-500 text-xs mt-1">Signature & Date</div>
            </div>
        </div>
        <div class="space-y-4 text-right">
            <div>
                <div class="mb-1 font-semibold">Date</div>
                <div class="border-b border-black w-32 inline-block">{{ \Carbon\Carbon::parse($date)->format('M d, Y')
                    }}</div>
            </div>
            <div>
                <div class="mb-1 font-semibold">Date</div>
                <div class="border-b border-black w-32 inline-block">{{ \Carbon\Carbon::parse($date)->format('M d, Y')
                    }}</div>
            </div>
            <div>
                <div class="mb-1 font-semibold">Date</div>
                <div class="border-b border-black w-32 inline-block">{{ \Carbon\Carbon::parse($date)->format('M d, Y')
                    }}</div>
            </div>
        </div>
    </div>
</div>