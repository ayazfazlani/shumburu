<div class="p-6 bg-white text-black max-w-7xl mx-auto border border-gray-300 rounded shadow">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded text-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="mb-4 p-3 bg-blue-100 border border-blue-300 text-blue-800 rounded text-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- Sync Results Panel --}}
    @if ($showSyncResults && !empty($syncResults))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-300 rounded text-xs">
            <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-yellow-800">📋 Sync Results</span>
                <button wire:click="dismissSyncResults" class="text-yellow-600 hover:text-yellow-800 text-xs underline">Dismiss</button>
            </div>
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left text-yellow-800">
                        <th class="pb-1">Material</th>
                        <th class="pb-1">Ledger Balance</th>
                        <th class="pb-1">Actual Stock</th>
                        <th class="pb-1">Adjustment</th>
                        <th class="pb-1">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($syncResults as $result)
                        <tr class="{{ $result['synced'] ? 'text-red-700 font-semibold' : 'text-green-700' }}">
                            <td class="py-0.5">{{ $result['material'] }}</td>
                            <td>{{ number_format($result['ledger_balance'], 2) }}</td>
                            <td>{{ number_format($result['actual_stock'], 2) }}</td>
                            <td>{{ $result['adjustment'] > 0 ? '+' : '' }}{{ number_format($result['adjustment'], 2) }}</td>
                            <td>{{ $result['action'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
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

            {{-- Sync Button --}}
            <button
                wire:click="syncStock"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                wire:target="syncStock"
                class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-semibold flex items-center gap-1"
                title="Sync stock quantities from transaction history (does not affect history)"
            >
                <svg wire:loading.remove wire:target="syncStock" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg wire:loading wire:target="syncStock" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="syncStock">Sync Stock</span>
                <span wire:loading wire:target="syncStock">Syncing...</span>
            </button>

            @if ($showInitialization || collect($rows)->where('has_transactions', false)->count() > 0)
                <button
                    wire:click="initializeStockData"
                    wire:loading.attr="disabled"
                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-xs font-semibold flex items-center gap-1"
                    title="Initialize opening balances for materials without transactions"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Initialize
                </button>
            @endif
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
                    <th class="border border-black p-1">Actual Stock (kg)</th>
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
                $totalActual = 0;
                @endphp

                @foreach($rows as $i => $row)
                @php
                $totalBeginning += $row['beginning'];
                $totalAddition += $row['addition'];
                $totalOut += $row['out'];
                $totalReturn += $row['return'];
                $totalWaste += $row['waste'];
                $totalEnding += $row['ending'];
                $totalActual += $row['current_stock'];
                @endphp
                <tr class="{{ $row['ending'] < 0 ? 'bg-red-50' : ($row['stock_mismatch'] ? 'bg-yellow-50' : '') }}">
                    <td class="border border-black p-1 text-center">{{ $i + 1 }}</td>
                    <td class="border border-black p-1 font-medium">
                        {{ $row['name'] }}
                        @if (!$row['has_transactions'])
                            <span class="text-orange-500 text-[10px]" title="No transactions">⚠</span>
                        @endif
                    </td>
                    <td class="border border-black p-1 text-right">{{ number_format($row['beginning'], 2) }}</td>
                    <td class="border border-black p-1 text-right text-green-600">+{{ number_format($row['addition'], 2) }}</td>
                    <td class="border border-black p-1 text-right text-red-600">-{{ number_format($row['out'], 2) }}</td>
                    <td class="border border-black p-1 text-right text-blue-600">+{{ number_format($row['return'], 2) }}</td>
                    <td class="border border-black p-1 text-right text-orange-600">-{{ number_format($row['waste'], 2) }}</td>
                    <td class="border border-black p-1 text-right font-semibold
                            {{ $row['ending'] < 0 ? 'text-red-600' : 'text-black' }}">
                        {{ number_format($row['ending'], 2) }}
                    </td>
                    <td class="border border-black p-1 text-right
                            {{ $row['stock_mismatch'] ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                        {{ number_format($row['current_stock'], 2) }}
                        @if ($row['stock_mismatch'])
                            <span class="text-[10px]">⚠</span>
                        @endif
                    </td>
                    <td class="border border-black p-1 text-center">
                        @if($row['ending'] < 0) <span class="text-red-600 font-semibold">SHORTAGE</span>
                            @elseif($row['stock_mismatch']) <span class="text-orange-600 font-semibold">MISMATCH</span>
                            @elseif(!$row['has_transactions']) <span class="text-yellow-600 font-semibold">NO DATA</span>
                            @elseif($row['ending'] < $row['min_stock']) <span class="text-orange-600 font-semibold">LOW STOCK</span>
                            @else
                                <span class="text-green-600">OK</span>
                            @endif
                    </td>
                    <td class="border border-black p-1 text-xs
                            {{ str_contains($row['remark'], '⚠') ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                        {{ $row['remark'] }}
                    </td>
                </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="bg-gray-100 font-semibold">
                    <td class="border border-black p-1 text-center" colspan="2">TOTAL</td>
                    <td class="border border-black p-1 text-right">{{ number_format($totalBeginning, 2) }}</td>
                    <td class="border border-black p-1 text-right text-green-600">+{{ number_format($totalAddition, 2) }}</td>
                    <td class="border border-black p-1 text-right text-red-600">-{{ number_format($totalOut, 2) }}</td>
                    <td class="border border-black p-1 text-right text-blue-600">+{{ number_format($totalReturn, 2) }}</td>
                    <td class="border border-black p-1 text-right text-orange-600">-{{ number_format($totalWaste, 2) }}</td>
                    <td class="border border-black p-1 text-right">{{ number_format($totalEnding, 2) }}</td>
                    <td class="border border-black p-1 text-right {{ abs($totalEnding - $totalActual) > 0.01 ? 'text-red-600 font-bold' : '' }}">
                        {{ number_format($totalActual, 2) }}
                    </td>
                    <td class="border border-black p-1 text-center">
                        @if($totalEnding < 0) <span class="text-red-600">CRITICAL</span>
                            @elseif(abs($totalEnding - $totalActual) > 0.01) <span class="text-orange-600">MISMATCH</span>
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