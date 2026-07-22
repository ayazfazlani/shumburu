<!-- resources/views/livewire/sales/dashboard.blade.php -->
<div class="bx-page bx-page-sales">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Sales Dashboard
            </h1>
            <p class="bx-header-subtitle">SPF-HDPE Pipe Factory - Sales & Customer Management</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-primary">Sales</span>
            <span class="bx-badge bx-badge-secondary">{{ auth()->user()->name }}</span>
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
            <div class="bx-stat-card-label">Production Orders</div>
            <div class="bx-stat-card-value text-primary">{{ $productionOrders->count() }}</div>
            <div class="bx-stat-card-desc">Total orders</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-warning">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Pending Orders</div>
            <div class="bx-stat-card-value text-warning">{{ $productionOrders->where('status', 'pending')->count() }}</div>
            <div class="bx-stat-card-desc">Awaiting approval</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Today's Deliveries</div>
            <div class="bx-stat-card-value text-success">{{ $deliveries->where('delivery_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Delivered today</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Today's Payments</div>
            <div class="bx-stat-card-value text-accent">{{ $payments->where('payment_date', today())->count() }}</div>
            <div class="bx-stat-card-desc">Received today</div>
        </div>
    </div>

    <!-- ─── QUICK ACTIONS ─── -->
    <div class="bx-quick-actions">
        <a href="{{ route('sales.create-order') }}" class="bx-btn bx-btn-primary bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Order
        </a>
        <a href="{{ route('sales.deliveries') }}" class="bx-btn bx-btn-success bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Record Delivery
        </a>
        <a href="{{ route('sales.payments') }}" class="bx-btn bx-btn-accent bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            Record Payment
        </a>
        <a href="{{ route('sales.reports') }}" class="bx-btn bx-btn-info bx-btn-block">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Sales Reports
        </a>
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="production-orders" onclick="switchSalesTab('production-orders')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Production Orders
            </button>
            <button class="bx-tab" data-tab="deliveries" onclick="switchSalesTab('deliveries')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Deliveries
            </button>
            <button class="bx-tab" data-tab="payments" onclick="switchSalesTab('payments')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Payments
            </button>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Production Orders ─── -->
    <div id="tab-production-orders" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Production Orders
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Requested Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productionOrders->take(5) as $order)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">{{ $order->order_number }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $order->customer->display_name }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => 'bx-badge-warning',
                                                'approved' => 'bx-badge-info',
                                                'in_production' => 'bx-badge-primary',
                                                'completed' => 'bx-badge-success',
                                                'delivered' => 'bx-badge-secondary',
                                            ];
                                            $sc = $statusConfig[$order->status] ?? 'bx-badge-gray';
                                        @endphp
                                        <span class="bx-badge {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                    </td>
                                    <td>{{ $order->requested_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="bx-actions">
                                            <button class="bx-action" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            @if($order->status === 'pending')
                                                <button class="bx-action bx-action-edit" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <h3>No production orders found</h3>
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
                    Deliveries
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Delivery Date</th>
                                <th>Delivered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries->take(5) as $delivery)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">{{ $delivery->productionOrder->order_number }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $delivery->customer->display_name }}</span>
                                    </td>
                                    <td class="font-mono font-bold text-success">${{ number_format($delivery->total_amount, 2) }}</td>
                                    <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                    <td>{{ $delivery->deliveredBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="bx-empty">
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
                    Payments
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
    function switchSalesTab(tabName) {
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
