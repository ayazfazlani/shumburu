<!-- resources/views/livewire/warehouse/dashboard.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Warehouse Management
        </h1>
        <p class="bx-header-subtitle">SPF-HDPE Pipe Factory - Material & Inventory Control</p>
    </div>

    <!-- ─── QUICK STATS ─── -->
    <div class="bx-stats-grid">
        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Today's Stock In</div>
            <div class="bx-stat-card-value text-primary">{{ $stockIns->where('received_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Materials received</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-secondary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Today's Stock Out</div>
            <div class="bx-stat-card-value text-secondary">{{ $stockOuts->where('issued_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Materials issued</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Finished Goods</div>
            <div class="bx-stat-card-value text-accent">{{ $finishedGoods->where('production_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Produced today</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-warning">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Scrap/Waste</div>
            <div class="bx-stat-card-value text-warning">{{ $scrapWaste->where('waste_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Records today</div>
        </div>
    </div>

    <!-- ─── QUICK ACTIONS ─── -->
    <div class="bx-quick-actions">
        @can('can perform material stock in')
            <a href="{{ route('warehouse.stock-in') }}" class="bx-btn bx-btn-primary bx-btn-block">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Stock In
            </a>
        @endcan

        @can('can perform material stock out')
            <a href="{{ route('warehouse.stock-out') }}" class="bx-btn bx-btn-secondary bx-btn-block">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                Stock Out
            </a>
        @endcan

        @can('can record finished goods')
            <a href="{{ route('warehouse.finished-goods') }}" class="bx-btn bx-btn-success bx-btn-block">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finished Goods
            </a>
        @endcan

        @can('can see scrap waste')
            <a href="{{ route('warehouse.scrap-wastes') }}" class="bx-btn bx-btn-warning bx-btn-block">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Scrap/Waste
            </a>
        @endcan
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="stock-ins" onclick="switchTab('stock-ins')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Stock Ins
            </button>
            <button class="bx-tab" data-tab="stock-outs" onclick="switchTab('stock-outs')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Stock Outs
            </button>
            <button class="bx-tab" data-tab="finished-goods" onclick="switchTab('finished-goods')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finished Goods
            </button>
            <button class="bx-tab" data-tab="scrap-waste" onclick="switchTab('scrap-waste')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Scrap/Waste
            </button>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Stock Ins ─── -->
    <div id="tab-stock-ins" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Recent Stock Ins
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
                                        <div class="bx-table-cell-with-avatar">
                                            <div class="bx-avatar bx-avatar-primary">
                                                <span>{{ substr($stockIn->rawMaterial->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $stockIn->rawMaterial->name }}</div>
                                                <div class="text-gray text-xs">{{ $stockIn->rawMaterial->code }}</div>
                                            </div>
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                            </div>
                                            <h3>No stock-in records found</h3>
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

    <!-- ─── TAB CONTENT: Stock Outs ─── -->
    <div id="tab-stock-outs" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Recent Stock Outs
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
                                <th>Status</th>
                                <th>Issued Date</th>
                                <th>Issued By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockOuts->take(5) as $stockOut)
                                <tr>
                                    <td>
                                        <div class="bx-table-cell-with-avatar">
                                            <div class="bx-avatar bx-avatar-secondary">
                                                <span>{{ substr($stockOut->rawMaterial->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $stockOut->rawMaterial->name }}</div>
                                                <div class="text-gray text-xs">{{ $stockOut->rawMaterial->code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($stockOut->quantity, 3) }}</td>
                                    <td><span class="bx-code">{{ $stockOut->batch_number }}</span></td>
                                    <td>
                                        @if ($stockOut->status === 'material_on_process')
                                            <span class="bx-badge bx-badge-warning">On Process</span>
                                        @elseif($stockOut->status === 'completed')
                                            <span class="bx-badge bx-badge-success">Completed</span>
                                        @else
                                            <span class="bx-badge bx-badge-danger">Scrapped</span>
                                        @endif
                                    </td>
                                    <td>{{ $stockOut->issued_date->format('M d, Y') }}</td>
                                    <td>{{ $stockOut->issuedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No stock-out records found</h3>
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

    <!-- ─── TAB CONTENT: Finished Goods ─── -->
    <div id="tab-finished-goods" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Finished Goods
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finishedGoods->take(5) as $finishedGood)
                                <tr>
                                    <td>
                                        <div class="bx-table-cell-with-avatar">
                                            <div class="bx-avatar bx-avatar-accent">
                                                <span>{{ $finishedGood->product->code }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $finishedGood->product->name }}</div>
                                                <div class="text-gray text-xs">{{ $finishedGood->product->size }} | {{ $finishedGood->product->pn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($finishedGood->quantity) }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3>No finished goods records found</h3>
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
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Recent Scrap/Waste
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                                    <td><span class="bx-code">{{ $scrap->materialStockOutLine->materialStockOut->batch_number ?? '-' }}</span></td>
                                    <td>{{ $scrap->materialStockOutLine->productionLine->name ?? '-' }}</td>
                                    <td class="font-mono">{{ number_format($scrap->materialStockOutLine->quantity_consumed ?? 0, 3) }}</td>
                                    <td class="font-mono">{{ number_format($scrap->quantity, 3) }}</td>
                                    <td><span class="bx-badge bx-badge-warning">{{ $scrap->reason }}</span></td>
                                    <td>{{ $scrap->waste_date ? \Carbon\Carbon::parse($scrap->waste_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $scrap->recordedBy->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
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
</div>

<script>
    function switchTab(tabName) {
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
