<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">🏢 Warehouse Management</h1>
                    <p class="text-base-content/70 mt-1">SPF-HDPE Pipe Factory - Material & Inventory Control</p>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-primary badge-lg">Warehouse</div>
                    <div class="badge badge-outline">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Stock In Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Today's Stock In</div>
                <div class="stat-value text-primary">{{ $stockIns->where('received_date', today())->count() }}</div>
                <div class="stat-desc">Materials received</div>
            </div>

            <!-- Stock Out Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="stat-title">Today's Stock Out</div>
                <div class="stat-value text-secondary">{{ $stockOuts->where('issued_date', today())->count() }}</div>
                <div class="stat-desc">Materials issued</div>
            </div>

            <!-- Finished Goods Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Finished Goods</div>
                <div class="stat-value text-accent">{{ $finishedGoods->where('production_date', today())->count() }}
                </div>
                <div class="stat-desc">Produced today</div>
            </div>

            <!-- Scrap/Waste Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Scrap/Waste</div>
                <div class="stat-value text-warning">{{ $scrapWaste->where('waste_date', today())->count() }}</div>
                <div class="stat-desc">Records today</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('warehouse.stock-in') }}" class="btn btn-primary btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Stock In
            </a>
            <a href="{{ route('warehouse.stock-out') }}" class="btn btn-secondary btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
                Stock Out
            </a>
            <a href="{{ route('warehouse.production.create') }}" class="btn btn-success btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Production
            </a>
            <a href="{{ route('warehouse.production.index') }}" class="btn btn-info btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Production Records
            </a>
            <a href="{{ route('warehouse.finished-goods') }}" class="btn btn-accent btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Finished Goods
            </a>
            <a href="{{ route('warehouse.scrap-wastes') }}" class="btn btn-warning btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                Scrap/Waste
            </a>
        </div>

        <!-- Recent Activity Tabs -->
        <div class="tabs tabs-boxed bg-base-100 shadow-lg mb-6">
            <a class="tab tab-active" onclick="showTab('stock-ins')">Recent Stock Ins</a>
            <a class="tab" onclick="showTab('stock-outs')">Recent Stock Outs</a>
            <a class="tab" onclick="showTab('finished-goods')">Recent Finished Goods</a>
            <a class="tab" onclick="showTab('scrap-waste')">Recent Scrap/Waste</a>
        </div>

        <!-- Stock Ins Table -->
        <div id="stock-ins" class="tab-content">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                            </path>
                        </svg>
                        Recent Stock Ins
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Quantity (kg)</th>
                                    <th>Batch Number</th>
                                    <th>Received Date</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockIns->take(5) as $stockIn)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-primary text-primary-content rounded-full w-8">
                                                        <span
                                                            class="text-xs">{{ substr($stockIn->rawMaterial->name, 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $stockIn->rawMaterial->name }}</div>
                                                    <div class="text-sm opacity-50">{{ $stockIn->rawMaterial->code }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($stockIn->quantity, 3) }}</td>
                                        <td><span class="badge badge-outline">{{ $stockIn->batch_number }}</span></td>
                                        <td>{{ $stockIn->received_date->format('M d, Y') }}</td>
                                        <td>{{ $stockIn->receivedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            No stock-in records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Outs Table -->
        <div id="stock-outs" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-secondary">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Recent Stock Outs
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Quantity (kg)</th>
                                    <th>Batch Number</th>
                                    <th>Status</th>
                                    <th>Issued Date</th>
                                    <th>Issued By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockOuts->take(5) as $stockOut)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-secondary text-secondary-content rounded-full w-8">
                                                        <span
                                                            class="text-xs">{{ substr($stockOut->rawMaterial->name, 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $stockOut->rawMaterial->name }}</div>
                                                    <div class="text-sm opacity-50">{{ $stockOut->rawMaterial->code }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($stockOut->quantity, 3) }}</td>
                                        <td><span class="badge badge-outline">{{ $stockOut->batch_number }}</span>
                                        </td>
                                        <td>
                                            @if ($stockOut->status === 'material_on_process')
                                                <span class="badge badge-warning">On Process</span>
                                            @elseif($stockOut->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-error">Scrapped</span>
                                            @endif
                                        </td>
                                        <td>{{ $stockOut->issued_date->format('M d, Y') }}</td>
                                        <td>{{ $stockOut->issuedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            No stock-out records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finished Goods Table -->
        <div id="finished-goods" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-accent">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Recent Finished Goods
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Batch Number</th>
                                    <th>Purpose</th>
                                    <th>Customer</th>
                                    <th>Production Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($finishedGoods->take(5) as $finishedGood)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-accent text-accent-content rounded-full w-8">
                                                        <span
                                                            class="text-xs">{{ $finishedGood->product->code }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $finishedGood->product->name }}</div>
                                                    <div class="text-sm opacity-50">{{ $finishedGood->product->size }}
                                                        | {{ $finishedGood->product->pn }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($finishedGood->quantity) }}</td>
                                        <td><span class="badge badge-outline">{{ $finishedGood->batch_number }}</span>
                                        </td>
                                        <td>
                                            @if ($finishedGood->purpose === 'for_stock')
                                                <span class="badge badge-info">For Stock</span>
                                            @else
                                                <span class="badge badge-primary">Customer Order</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($finishedGood->customer)
                                                <span
                                                    class="badge badge-outline">{{ $finishedGood->customer->display_name }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $finishedGood->production_date->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            No finished goods records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrap/Waste Table -->
        <div id="scrap-waste" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-warning">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Recent Scrap/Waste
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Batch</th>
                                    <th>Line</th>
                                    <th>Qty Used</th>
                                    <th>Scrap Qty</th>
                                    <th>Reason</th>
                                    <th>Waste Date</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scrapWaste->take(5) as $scrap)
                                    <tr>
                                        <td>
                                            <span class="badge badge-outline">
                                                {{ $scrap->materialStockOutLine->materialStockOut->batch_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $scrap->materialStockOutLine->productionLine->name ?? '-' }}
                                        </td>
                                        <td class="font-mono">
                                            {{ number_format($scrap->materialStockOutLine->quantity_consumed ?? 0, 3) }}
                                        </td>
                                        <td class="font-mono">
                                            {{ number_format($scrap->quantity, 3) }}
                                        </td>
                                        <td><span
                                                class="badge badge-warning badge-outline">{{ $scrap->reason }}</span>
                                        </td>
                                        <td>{{ $scrap->waste_date ? \Carbon\Carbon::parse($scrap->waste_date)->format('M d, Y') : '-' }}
                                        </td>
                                        <td>{{ $scrap->recordedBy->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            No scrap/waste records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('tab-active');
        });

        // Show selected tab content
        document.getElementById(tabName).classList.remove('hidden');

        // Add active class to clicked tab
        event.target.classList.add('tab-active');
    }
</script>
