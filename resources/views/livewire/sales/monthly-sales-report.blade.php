<div class="p-6 bg-white text-black max-w-6xl mx-auto border border-gray-300 rounded shadow">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <x-app-logo class="w-16 h-16" />
            <div>
                <div class="font-bold text-lg">SHUMBRO PLASTIC FACTORY</div>
                <div class="text-xs">Monthly Sales Report</div>
            </div>
        </div>
        <div class="text-right text-xs">
            <div><span class="font-semibold">Document No:</span> SPF-SR-003</div>
            <div><span class="font-semibold">Period:</span> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 justify-between text-xs mb-2">
        <div class="flex items-center gap-2">
            <span class="font-semibold">Filters:</span>
            <input type="date" wire:model.live="startDate" class="border rounded px-2 py-1 text-xs" />
            <input type="date" wire:model.live="endDate" class="border rounded px-2 py-1 text-xs" />
            <select wire:model.live="customer_id" class="border rounded px-2 py-1 text-xs">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="product_id" class="border rounded px-2 py-1 text-xs">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportToPdf" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                Export PDF
            </button>
            {{-- <button wire:click="printReport" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                Print
            </button> --}}
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-blue-50 p-3 rounded border text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $summary['total_orders'] }}</div>
            <div class="text-xs text-gray-600">Total Orders</div>
        </div>
        <div class="bg-green-50 p-3 rounded border text-center">
            <div class="text-2xl font-bold text-green-600">{{ $summary['total_deliveries'] }}</div>
            <div class="text-xs text-gray-600">Total Deliveries</div>
        </div>
        <div class="bg-purple-50 p-3 rounded border text-center">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($summary['total_net_weight'], 2) }}</div>
            <div class="text-xs text-gray-600">Total Net Weight (kg)</div>
        </div>
        <div class="bg-orange-50 p-3 rounded border text-center">
            <div class="text-2xl font-bold text-orange-600">${{ number_format($summary['total_sales'], 2) }}</div>
            <div class="text-xs text-gray-600">Total Sales</div>
        </div>
    </div>

    <div class="overflow-x-auto mt-2">
        <table class="w-full text-xs border border-collapse border-black">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-black px-2 py-1">Date</th>
                    <th class="border border-black px-2 py-1">Customer Name</th>
                    <th class="border border-black px-2 py-1">Item Description</th>
                    <th class="border border-black px-2 py-1">Unit of Meas.</th>
                    <th class="border border-black px-2 py-1">Qty</th>
                    <th class="border border-black px-2 py-1">Net Weight</th>
                    <th class="border border-black px-2 py-1">Sales Price</th>
                    <th class="border border-black px-2 py-1">Total</th>
                    <th class="border border-black px-2 py-1">Remark</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalNetWeight = 0;
                    $totalSales = 0;
                @endphp
                
                @forelse($groupedSales as $sale)
                    @php
                        $totalNetWeight += $sale['net_weight'];
                        $totalSales += $sale['total'];
                    @endphp
                    <tr>
                        <td class="border border-black px-2 py-1">{{ \Carbon\Carbon::parse($sale['date'])->format('d/m/Y') }}</td>
                        <td class="border border-black px-2 py-1">{{ $sale['customer_name'] }}</td>
                        <td class="border border-black px-2 py-1">{{ $sale['item_description'] }}</td>
                        <td class="border border-black px-2 py-1">{{ $sale['unit_measurement'] }}</td>
                        <td class="border border-black px-2 py-1 text-right">{{ number_format($sale['quantity']) }}</td>
                        <td class="border border-black px-2 py-1 text-right">{{ number_format($sale['net_weight'], 2) }}</td>
                        <td class="border border-black px-2 py-1 text-right">{{ number_format($sale['sales_price'], 2) }}</td>
                        <td class="border border-black px-2 py-1 text-right">{{ number_format($sale['total'], 2) }}</td>
                        <td class="border border-black px-2 py-1">{{ $sale['remark'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="border border-black px-2 py-1 text-center text-gray-500">
                            No sales data found for the selected period
                        </td>
                    </tr>
                @endforelse
                
                <!-- Summary Row -->
                <tr class="font-bold bg-gray-200">
                    <td colspan="5" class="border border-black px-2 py-1 text-center">Total</td>
                    <td class="border border-black px-2 py-1 text-right">{{ number_format($totalNetWeight, 2) }}</td>
                    <td class="border border-black px-2 py-1"></td>
                    <td class="border border-black px-2 py-1 text-right">{{ number_format($totalSales, 2) }}</td>
                    <td class="border border-black px-2 py-1"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Signature Section -->
    <div class="mt-8 flex justify-between">
        <div class="text-center">
            <div class="border-t border-black w-32 inline-block"></div>
            <div class="text-xs mt-1">Prepared By</div>
        </div>
        <div class="text-center">
            <div class="border-t border-black w-32 inline-block"></div>
            <div class="text-xs mt-1">Checked By</div>
        </div>
        <div class="text-center">
            <div class="border-t border-black w-32 inline-block"></div>
            <div class="text-xs mt-1">Approved By</div>
        </div>
    </div>
</div>

