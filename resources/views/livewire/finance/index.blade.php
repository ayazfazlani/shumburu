<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-accent">💰 Finance Dashboard</h1>
                    <p class="text-base-content/70 mt-1">SPF-HDPE Pipe Factory - Financial Reports & Analytics</p>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-accent badge-lg">Finance</div>
                    <div class="badge badge-outline">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-success">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value text-success">${{ number_format($payments->sum('amount'), 2) }}</div>
                <div class="stat-desc">All time</div>
            </div>

            <!-- Today's Revenue -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="stat-title">Today's Revenue</div>
                <div class="stat-value text-accent">
                    ${{ number_format($payments->where('payment_date', today())->sum('amount'), 2) }}</div>
                <div class="stat-desc">Today's payments</div>
            </div>

            <!-- Finished Goods Value -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-info">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="stat-title">Finished Goods</div>
                <div class="stat-value text-info">{{ $finishedGoods->count() }}</div>
                <div class="stat-desc">Total produced</div>
            </div>

            <!-- Raw Materials Value -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Raw Materials</div>
                <div class="stat-value text-warning">{{ $stockIns->count() }}</div>
                <div class="stat-desc">Stock-in records</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('finance.revenue-report') }}" class="btn btn-success btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Revenue Report
            </a>
            <a href="{{ route('finance.inventory-report') }}" class="btn btn-info btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Inventory Report
            </a>
            <a href="{{ route('finance.waste-report') }}" class="btn btn-warning btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                Waste Report
            </a>
        </div>

        <!-- Financial Reports Tabs -->
        <div class="tabs tabs-boxed bg-base-100 shadow-lg mb-6">
            <a class="tab tab-active" onclick="showTab('finished-goods')">Finished Goods</a>
            <a class="tab" onclick="showTab('stock-ins')">Raw Materials</a>
            <a class="tab" onclick="showTab('scrap-waste')">Scrap/Waste</a>
            <a class="tab" onclick="showTab('deliveries')">Deliveries</a>
            <a class="tab" onclick="showTab('payments')">Payments</a>
        </div>

        <!-- Finished Goods Table -->
        <div id="finished-goods" class="tab-content">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-info">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Finished Goods Inventory
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
                                    <th>Produced By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($finishedGoods->take(5) as $finishedGood)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-info text-info-content rounded-full w-8">
                                                        <span class="text-xs">{{ $finishedGood->product->code }}</span>
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
                                        <td>{{ $finishedGood->producedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            No finished goods found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Raw Materials Table -->
        <div id="stock-ins" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-warning">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Raw Materials Stock
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
                                                    <div class="bg-warning text-warning-content rounded-full w-8">
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
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                            No raw materials found
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
                    <h2 class="card-title text-error">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Scrap/Waste Analysis
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Quantity (kg)</th>
                                    <th>Reason</th>
                                    <th>Waste Date</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scrapWaste->take(5) as $scrap)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-error text-error-content rounded-full w-8">
                                                        <span
                                                            class="text-xs">{{ substr($scrap->rawMaterial->name, 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $scrap->rawMaterial->name }}</div>
                                                    <div class="text-sm opacity-50">{{ $scrap->rawMaterial->code }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($scrap->quantity, 3) }}</td>
                                        <td><span class="badge badge-error badge-outline">{{ $scrap->reason }}</span>
                                        </td>
                                        <td>{{ $scrap->waste_date->format('M d, Y') }}</td>
                                        <td>{{ $scrap->recordedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-base-content/50">
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

        <!-- Deliveries Table -->
        <div id="deliveries" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-success">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Delivery Reports
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                    <th>Delivery Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveries->take(5) as $delivery)
                                    <tr>
                                        <td>
                                            <span
                                                class="font-mono font-bold">{{ $delivery->productionOrder->order_number }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-outline">{{ $delivery->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ $delivery->product->name }}</span>
                                                <span class="badge badge-sm">{{ $delivery->product->code }}</span>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($delivery->quantity) }}</td>
                                        <td class="font-mono">${{ number_format($delivery->unit_price, 2) }}</td>
                                        <td class="font-mono font-bold text-success">
                                            ${{ number_format($delivery->total_amount, 2) }}</td>
                                        <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            No deliveries found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div id="payments" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-accent">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Payment Reports
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Bank Slip Ref</th>
                                    <th>Proforma Invoice</th>
                                    <th>Payment Date</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments->take(5) as $payment)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge badge-outline">{{ $payment->customer->display_name }}</span>
                                        </td>
                                        <td class="font-mono font-bold text-accent">
                                            ${{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge badge-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if ($payment->bank_slip_reference)
                                                <span
                                                    class="badge badge-success">{{ $payment->bank_slip_reference }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->proforma_invoice_number)
                                                <span
                                                    class="badge badge-warning">{{ $payment->proforma_invoice_number }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ $payment->recordedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            No payments found
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
