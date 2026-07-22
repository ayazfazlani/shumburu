<!-- resources/views/livewire/finance/dashboard.blade.php -->
<div class="bx-page bx-page-finance">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Finance Dashboard
            </h1>
            <p class="bx-header-subtitle">SPF-HDPE Pipe Factory - Financial Reports & Analytics</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-accent">Finance</span>
            <span class="bx-badge bx-badge-secondary">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats-grid">
        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Total Revenue</div>
            <div class="bx-stat-card-value text-success">${{ number_format($payments->sum('amount'), 2) }}</div>
            <div class="bx-stat-card-desc">All time</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Today's Revenue</div>
            <div class="bx-stat-card-value text-accent">${{ number_format($payments->where('payment_date', today())->sum('amount'), 2) }}</div>
            <div class="bx-stat-card-desc">Today's payments</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-info">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Finished Goods</div>
            <div class="bx-stat-card-value text-info">{{ $finishedGoods->count() }}</div>
            <div class="bx-stat-card-desc">Total produced</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-warning">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Raw Materials</div>
            <div class="bx-stat-card-value text-warning">{{ $stockIns->count() }}</div>
            <div class="bx-stat-card-desc">Stock-in records</div>
        </div>
    </div>

    <!-- ─── QUICK ACTIONS ─── -->
    <div class="bx-quick-actions">
        <a href="{{ route('finance.revenue-report') }}" class="bx-btn bx-btn-success bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Revenue Report
        </a>
        <a href="{{ route('finance.inventory-report') }}" class="bx-btn bx-btn-info bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Inventory Report
        </a>

    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="finished-goods" onclick="switchFinanceTab('finished-goods')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Finished Goods
            </button>
            <button class="bx-tab" data-tab="stock-ins" onclick="switchFinanceTab('stock-ins')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Raw Materials
            </button>
            <button class="bx-tab" data-tab="scrap-waste" onclick="switchFinanceTab('scrap-waste')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Scrap/Waste
            </button>
            <button class="bx-tab" data-tab="deliveries" onclick="switchFinanceTab('deliveries')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Deliveries
            </button>
            <button class="bx-tab" data-tab="payments" onclick="switchFinanceTab('payments')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Payments
            </button>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Finished Goods ─── -->
    <div id="tab-finished-goods" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Finished Goods Inventory
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                        <div class="bx-product-cell">
                                            <span class="bx-product-name">{{ $finishedGood->product->name }}</span>
                                            <span class="bx-product-code">{{ $finishedGood->product->size }} | {{ $finishedGood->product->pn }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono font-bold">{{ number_format($finishedGood->quantity) }}</td>
                                    <td><span class="bx-code">{{ $finishedGood->batch_number }}</span></td>
                                    <td>
                                        @if ($finishedGood->purpose === 'for_stock')
                                            <span class="bx-badge bx-badge-info">For Stock</span>
                                        @else
                                            <span class="bx-badge bx-badge-primary">Customer Order</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($finishedGood->customer)
                                            <span class="bx-code">{{ $finishedGood->customer->display_name }}</span>
                                        @else
                                            <span class="text-gray">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $finishedGood->production_date->format('M d, Y') }}</td>
                                    <td>{{ $finishedGood->producedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No finished goods found</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Raw Materials ─── -->
    <div id="tab-stock-ins" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Raw Materials Stock
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                        <div class="bx-material-cell">
                                            <span class="bx-material-name">{{ $stockIn->rawMaterial->name }}</span>
                                            <span class="bx-material-code">{{ $stockIn->rawMaterial->code }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($stockIn->quantity, 3) }}</td>
                                    <td><span class="bx-code">{{ $stockIn->batch_number }}</span></td>
                                    <td>{{ $stockIn->received_date->format('M d, Y') }}</td>
                                    <td>{{ $stockIn->receivedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                            </div>
                                            <h3>No raw materials found</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Scrap/Waste ─── -->
    <div id="tab-scrap-waste" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Scrap/Waste Analysis
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                        <div class="bx-material-cell">
                                            <span class="bx-material-name">{{ $scrap->rawMaterial->name ?? 'N/A' }}</span>
                                            <span class="bx-material-code">{{ $scrap->rawMaterial->code ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($scrap->quantity, 3) }}</td>
                                    <td><span class="bx-badge bx-badge-danger">{{ $scrap->reason }}</span></td>
                                    <td>{{ \Carbon\Carbon::make($scrap->waste_date)->format('M d, Y') }}</td>
                                    <td>{{ $scrap->recordedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </div>
                                            <h3>No scrap/waste records found</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Deliveries ─── -->
    <div id="tab-deliveries" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Delivery Reports
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                        <span class="bx-order-number">#{{ $delivery->productionOrder->order_number }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $delivery->customer->display_name }}</span>
                                    </td>
                                    <td>
                                        <div class="bx-product-cell">
                                            <span class="bx-product-name">{{ $delivery->product->name }}</span>
                                            <span class="bx-product-code">{{ $delivery->product->code }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($delivery->quantity) }}</td>
                                    <td class="font-mono">${{ number_format($delivery->unit_price, 2) }}</td>
                                    <td class="font-mono font-bold text-success">${{ number_format($delivery->total_amount, 2) }}</td>
                                    <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No deliveries found</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Payments ─── -->
    <div id="tab-payments" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Payment Reports
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                        <span class="bx-code">{{ $payment->customer->display_name }}</span>
                                    </td>
                                    <td class="font-mono font-bold text-accent">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="bx-badge bx-badge-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($payment->bank_slip_reference)
                                            <span class="bx-badge bx-badge-success">{{ $payment->bank_slip_reference }}</span>
                                        @else
                                            <span class="text-gray">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->proforma_invoice_number)
                                            <span class="bx-badge bx-badge-warning">{{ $payment->proforma_invoice_number }}</span>
                                        @else
                                            <span class="text-gray">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>{{ $payment->recordedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                            </div>
                                            <h3>No payments found</h3>
                                        </div>
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

<script>
    function switchFinanceTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.bx-tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.bx-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected tab content
        const targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.classList.add('active');
        }

        // Add active class to clicked tab
        const targetTab = document.querySelector(`.bx-tab[data-tab="${tabName}"]`);
        if (targetTab) {
            targetTab.classList.add('active');
        }
    }
</script>
