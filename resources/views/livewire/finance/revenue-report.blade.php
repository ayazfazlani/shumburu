<!-- resources/views/livewire/finance/revenue-report.blade.php -->
<div class="bx-page bx-page-revenue">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Revenue Report
            </h1>
            <p class="bx-header-subtitle">Track payments and deliveries revenue</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-secondary">{{ $payments->total() }} Payments</span>
            <span class="bx-badge bx-badge-success">{{ $deliveries->total() }} Deliveries</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Revenue</div>
            <div class="bx-stat-value text-success">${{ number_format($payments->sum('amount'), 2) }}</div>
            <div class="bx-stat-desc">From all payments</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Deliveries</div>
            <div class="bx-stat-value text-blue">{{ $deliveries->total() }}</div>
            <div class="bx-stat-desc">Completed deliveries</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today's Revenue</div>
            <div class="bx-stat-value text-warning">${{ number_format($payments->where('payment_date', today())->sum('amount'), 2) }}</div>
            <div class="bx-stat-desc">Today's payments</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">This Month</div>
            <div class="bx-stat-value text-accent">${{ number_format($payments->where('payment_date', '>=', now()->startOfMonth())->sum('amount'), 2) }}</div>
            <div class="bx-stat-desc">Monthly revenue</div>
        </div>
    </div>

    <!-- ─── FILTERS ─── -->
    <div class="bx-filters-bar">
        <div class="bx-filters-left">
            <div class="bx-filter-group">
                <label class="bx-filter-label">Search</label>
                <div class="bx-search">
                    <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="paymentSearch" type="text" placeholder="Search payments..." class="bx-search-input" />
                </div>
            </div>
            <div class="bx-filter-group">
                <label class="bx-filter-label">Date From</label>
                <input wire:model.live="dateFrom" type="date" class="bx-filter-input" />
            </div>
            <div class="bx-filter-group">
                <label class="bx-filter-label">Date To</label>
                <input wire:model.live="dateTo" type="date" class="bx-filter-input" />
            </div>
        </div>
        <div class="bx-filters-right">
            <button wire:click="exportReport" class="bx-btn bx-btn-export">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="payments" onclick="switchReportTab('payments')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Payments
                <span class="bx-tab-badge">{{ $payments->total() }}</span>
            </button>
            <button class="bx-tab" data-tab="deliveries" onclick="switchReportTab('deliveries')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Deliveries
                <span class="bx-tab-badge">{{ $deliveries->total() }}</span>
            </button>
        </div>
    </div>

    <!-- ─── TAB: PAYMENTS ─── -->
    <div id="tab-payments" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Payments
                </h3>
                <span class="bx-badge bx-badge-secondary">Total: ${{ number_format($payments->sum('amount'), 2) }}</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Delivery</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="bx-code">{{ $payment->customer->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-order-number">{{ $payment->productionOrder->order_number ?? '-' }}</span>
                                    </td>
                                    <td class="font-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::make($payment->payment_date)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                            </div>
                                            <h3>No payments found</h3>
                                            <p>Try adjusting your search or filter criteria.</p>
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

    <!-- ─── TAB: DELIVERIES ─── -->
    <div id="tab-deliveries" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Batch</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $delivery)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">#{{ $delivery->productionOrder->order_number ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-code">{{ $delivery->customer->name ?? '-' }}</span>
                                    </td>
                                    <td>{{ $delivery->product->name ?? '-' }}</td>
                                    <td class="font-bold">{{ number_format($delivery->quantity, 2) }}</td>
                                    <td><span class="bx-code">{{ $delivery->batch_number ?? '-' }}</span></td>
                                    <td>{{ \Carbon\Carbon::make($delivery->delivery_date)->format('d M Y') }}</td>
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
                                            <h3>No deliveries found</h3>
                                            <p>Try adjusting your search or filter criteria.</p>
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
                        {{ $deliveries->links('components.pagination') }}
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
