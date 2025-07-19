<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow" id="report-container">
    <!-- Header Section -->
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
            <div><span class="font-semibold">Effective date:</span> {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2 print:hidden">
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
            <button wire:click="printReport" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                Print
            </button>
            <button wire:click="exportToPdf" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                Export PDF
            </button>
        </div>
        <div><span class="font-semibold">page 1 of 1</span></div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="date,shift,product_id" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        <span class="ml-2 text-sm text-gray-600">Loading report...</span>
    </div>

    <!-- Report Table -->
    <div class="overflow-x-auto" wire:loading.remove wire:target="date,shift,product_id">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th rowspan="2" class="border border-black align-middle p-1">Raw Material</th>
                    <th rowspan="2" class="border border-black align-middle p-1">Quantity Consumed</th>
                    <th rowspan="2" class="border border-black align-middle p-1">Shift</th>
                    <th rowspan="2" class="border border-black align-middle p-1">Product</th>
                    <th rowspan="2" class="border border-black align-middle p-1">Size</th>
                    @foreach($lengths as $length)
                        <th class="border border-black p-1">{{ $length }}m</th>
                    @endforeach
                    <th rowspan="2" class="border border-black align-middle p-1">Total Weight (kg)</th>
                    <th rowspan="2" class="border border-black align-middle p-1">Average Weight (kg/roll)</th>
                </tr>
                <tr class="bg-gray-100">
                    @foreach($lengths as $length)
                        <th class="border border-black p-1">Quantity</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(count($reportData) > 0)
                    @foreach($reportData as $key => $row)
                        @if($key !== 'grand_totals')
                            <tr class="hover:bg-gray-50">
                                <td class="border border-black p-1">{{ $row['raw_material'] }}</td>
                                <td class="border border-black p-1 text-center">{{ $row['quantity_consumed'] }}</td>
                                <td class="border border-black p-1 text-center">{{ $row['shift'] }}</td>
                                <td class="border border-black p-1">{{ $row['product_name'] }}</td>
                                <td class="border border-black p-1 text-center">{{ $row['size'] }}</td>
                                @foreach($lengths as $length)
                                    <td class="border border-black p-1 text-center">
                                        {{ $row['length_quantities'][$length] > 0 ? $row['length_quantities'][$length] : '' }}
                                    </td>
                                @endforeach
                                <td class="border border-black p-1 text-right">{{ number_format($row['total_weight'], 2) }}</td>
                                <td class="border border-black p-1 text-right">{{ number_format($row['average_weight'], 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    
                    <!-- Grand Totals Row -->
                    @if(isset($reportData['grand_totals']))
                        <tr class="font-bold bg-gray-200">
                            <td class="border border-black p-1">Total</td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1"></td>
                            @foreach($lengths as $length)
                                <td class="border border-black p-1 text-center">
                                    {{ $reportData['grand_totals']['length_totals'][$length] }}
                                </td>
                            @endforeach
                            <td class="border border-black p-1 text-right">
                                {{ number_format($reportData['grand_totals']['grand_total_weight'], 2) }}
                            </td>
                            <td class="border border-black p-1"></td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="{{ 7 + count($lengths) }}" class="border border-black p-4 text-center text-gray-500">
                            No data found for the selected filters
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Quality Comments Section -->
    <div class="mt-4 text-xs">
        <div class="font-semibold underline mb-1">Comment of Quality</div>
        <div class="mb-2">
            Today all products were produced according to the standards and in a good quality, but we have 
            observed some problems, corrective actions and recommended the following for the next products:-
        </div>
        
        <div class="font-semibold underline mb-1">Problems:</div>
        <ul class="list-disc pl-5 mb-2">
            <li>32mm PN16 product had a problem of weight (over), thickness variation due to weight, deformation and blue stripe fluctuation.</li>
            <li>125mm PN20 pipes had a problem of weight (over), thickness variation due to weight, shrinkage of outer diameter and length (length=0.02m and OD=0.3mm), blue stripe fluctuation problem, crack, surface roughness, power outage and OD problem.</li>
            <li>32mm PN10 product had a problem of weight over due to the standards, surface roughness, power outage, breakage and blue stripe fluctuation and 25mm PN16 product had a problem of weight over due to the standards, power outage, thickness problem due to weight, surface roughness and blue stripe fluctuation.</li>
        </ul>
        
        <div class="font-semibold underline mb-1">Corrective action:</div>
        <div class="mb-2">
            Most of the problems were reduced by communicating with the shift leader and operator. However, 
            the weight problem was reduced but not eliminated because of the thickness of the products did not 
            fulfill the standard parameter when it was produced in the standard weight, so in order to reduce 
            this problem we increased the weight by prioritizing the thickness of the products.
        </div>
        
        <div class="font-semibold underline mb-1">Remark:</div>
        <div class="mb-2">
            As quality we recommended that the double type raw materials quality (purity and density) should be checked.
        </div>
    </div>

    <!-- Signatures Section -->
    <div class="mt-6 flex flex-wrap justify-between text-xs">
        <div>
            <div class="mb-1">Prepared by <span class="underline">Yohannes Choma</span></div>
            <div class="mb-1">Checked by <span class="underline">Yeshiamb A.</span></div>
            <div>Approved by <span class="underline">Aschalew</span></div>
        </div>
        <div class="text-right">
            <div class="mb-1">Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
            <div class="mb-1">Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
            <div>Date <span class="underline">{{ now()->format('d-m-Y') }}</span></div>
        </div>
    </div>


<!-- Print Styles -->
<style>
    @media print {
        .print\\:hidden {
            display: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        #report-container {
            max-width: none;
            margin: 0;
            padding: 20px;
            border: none;
            box-shadow: none;
        }
        
        table {
            page-break-inside: avoid;
        }
        
        .page-break {
            page-break-before: always;
        }
    }
</style>
</div>
<!-- JavaScript for Print Functionality -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('print-report', () => {
            window.print();
        });
    });
</script>