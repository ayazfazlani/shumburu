<!-- resources/views/livewire/sales/reports.blade.php -->
<div class="bx-page bx-page-sales-reports">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Sales Reports
            </h1>
            <p class="bx-header-subtitle">SPF-HDPE Pipe Factory - Sales Analytics & Reports</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-primary">Reports</span>
            <span class="bx-badge bx-badge-secondary">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- ─── REPORT CONTROLS ─── -->
    <div class="bx-card bx-card-filters">
        <div class="bx-card-header">
            <h3>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Report Settings
            </h3>
        </div>
        <div class="bx-card-body">
            <div class="bx-filters-grid">
                <div class="bx-filter-group">
                    <label class="bx-filter-label">Report Type</label>
                    <select wire:model.live="reportType" class="bx-select">
                        <option value="daily">Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly">Monthly Report</option>
                    </select>
                </div>

                <div class="bx-filter-group">
                    <label class="bx-filter-label">Start Date</label>
                    <input wire:model.live="startDate" type="date" class="bx-filter-input" />
                </div>

                <div class="bx-filter-group">
                    <label class="bx-filter-label">End Date</label>
                    <input wire:model.live="endDate" type="date" class="bx-filter-input" />
                </div>

                <div class="bx-filter-group bx-filter-actions">
                    <button wire:click="exportReport" class="bx-btn bx-btn-export">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats-grid">
        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Delivered Orders</div>
            <div class="bx-stat-card-value text-primary">{{ $summary['total_orders'] }}</div>
            <div class="bx-stat-card-desc">{{ ucfirst($reportType) }} period</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Deliveries</div>
            <div class="bx-stat-card-value text-success">{{ $summary['total_deliveries'] }}</div>
            <div class="bx-stat-card-desc">{{ ucfirst($reportType) }} period</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Payments</div>
            <div class="bx-stat-card-value text-accent">{{ $summary['total_payments'] }}</div>
            <div class="bx-stat-card-desc">{{ ucfirst($reportType) }} period</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-info">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Total Revenue</div>
            <div class="bx-stat-card-value text-info">${{ number_format($summary['total_payment_value'], 2) }}</div>
            <div class="bx-stat-card-desc">{{ ucfirst($reportType) }} period</div>
        </div>
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="delivered-orders" onclick="switchReportTab('delivered-orders')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Delivered Orders
            </button>
            <button class="bx-tab" data-tab="deliveries" onclick="switchReportTab('deliveries')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Deliveries
            </button>
            <button class="bx-tab" data-tab="payments" onclick="switchReportTab('payments')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Payments
            </button>
        </div>
    </div>

    <!-- ─── TAB: DELIVERED ORDERS ─── -->
    <div id="tab-delivered-orders" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Delivered Orders
                </h3>
                <span class="bx-badge bx-badge-secondary">{{ $deliveredOrders->total() }} Orders</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Products</th>
                                <th>Total Value</th>
                                <th>Payment Progress</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveredOrders as $order)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">{{ $order->order_number }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $order->customer?->display_name ?? 'Unknown Customer' }}</span>
                                    </td>
                                    <td>
                                        <div class="bx-products-list">
                                            @foreach($order->items->take(2) as $item)
                                                <div class="bx-product-item">
                                                    <span class="bx-product-name">{{ $item->product->name }}</span>
                                                    <span class="bx-code bx-code-xs">{{ $item->formatted_quantity }}</span>
                                                </div>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <span class="bx-more-items">+{{ $order->items->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="font-bold text-success">${{ number_format($order->items->sum('total_price'), 2) }}</td>
                                    <td>
                                        @php
                                            $totalPaid = $order->payments->sum('amount');
                                            $totalValue = $order->items->sum('total_price');
                                            $progress = $totalValue > 0 ? ($totalPaid / $totalValue) * 100 : 0;
                                        @endphp
                                        <div class="bx-progress-wrapper">
                                            <div class="bx-progress-bar">
                                                <div class="bx-progress-fill bx-progress-success" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="bx-progress-label">{{ number_format($progress, 1) }}%</span>
                                        </div>
                                        <div class="bx-progress-text">${{ number_format($totalPaid, 2) }} / ${{ number_format($totalValue, 2) }}</div>
                                    </td>
                                    <td>{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="bx-badge bx-badge-success">Delivered</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <h3>No delivered orders found</h3>
                                            <p>Try adjusting your date range or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($deliveredOrders->hasPages())
                <div class="bx-pagination-wrap">
                    <div class="bx-pagination-info">
                        Showing <strong>{{ $deliveredOrders->firstItem() ?? 0 }}</strong>
                        to <strong>{{ $deliveredOrders->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $deliveredOrders->total() }}</strong> orders
                    </div>
                    <div class="bx-pagination">
                        {{ $deliveredOrders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ─── TAB: DELIVERIES ─── -->
    <div id="tab-deliveries" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Deliveries
                </h3>
                <span class="bx-badge bx-badge-secondary">{{ $deliveries->total() }} Deliveries</span>
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
                                <th>Delivered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $delivery)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">{{ $delivery->productionOrder->order_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $delivery->customer?->display_name ?? 'Unknown Customer' }}</span>
                                    </td>
                                    <td>
                                        <div class="bx-product-cell">
                                            <span class="bx-product-name">{{ $delivery->product?->name ?? 'N/A' }}</span>
                                            <span class="bx-product-code">{{ $delivery->product?->code ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ number_format($delivery->quantity, 2) }}</td>
                                    <td class="font-mono">${{ number_format($delivery->unit_price, 2) }}</td>
                                    <td class="font-bold text-success">${{ number_format($delivery->total_amount, 2) }}</td>
                                    <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $delivery->deliveredBy?->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No deliveries found</h3>
                                            <p>Try adjusting your date range or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($deliveries->hasPages())
                <div class="bx-pagination-wrap">
                    <div class="bx-pagination-info">
                        Showing <strong>{{ $deliveries->firstItem() ?? 0 }}</strong>
                        to <strong>{{ $deliveries->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $deliveries->total() }}</strong> deliveries
                    </div>
                    <div class="bx-pagination">
                        {{ $deliveries->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ─── TAB: PAYMENTS ─── -->
    <div id="tab-payments" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Payments
                </h3>
                <span class="bx-badge bx-badge-secondary">{{ $payments->total() }} Payments</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Order Number</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Bank Slip Ref</th>
                                <th>Proforma Invoice</th>
                                <th>Payment Date</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="bx-code">{{ $payment->customer?->display_name ?? 'Unknown Customer' }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-order-number">{{ $payment->productionOrder->order_number ?? 'N/A' }}</span>
                                    </td>
                                    <td class="font-bold text-accent">${{ number_format($payment->amount, 2) }}</td>
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
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $payment->recordedBy?->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                            </div>
                                            <h3>No payments found</h3>
                                            <p>Try adjusting your date range or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
                <div class="bx-pagination-wrap">
                    <div class="bx-pagination-info">
                        Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong>
                        to <strong>{{ $payments->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $payments->total() }}</strong> payments
                    </div>
                    <div class="bx-pagination">
                        {{ $payments->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function switchReportTab(tabName) {
        document.querySelectorAll('.bx-tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelectorAll('.bx-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        const targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.classList.add('active');
        }
        const targetTab = document.querySelector(`.bx-tab[data-tab="${tabName}"]`);
        if (targetTab) {
            targetTab.classList.add('active');
        }
    }
</script>
