<!-- resources/views/livewire/operations/production-orders.blade.php -->
<div class="bx-page bx-page-production-orders">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Production Orders Management
            </h1>
            <p class="bx-header-subtitle">Manage production order status and track progress</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-secondary">{{ $orders->total() }} Orders</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Orders</div>
            <div class="bx-stat-value">{{ $orders->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Pending</div>
            <div class="bx-stat-value text-warning">{{ $orders->where('status', 'pending')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">In Production</div>
            <div class="bx-stat-value text-blue">{{ $orders->where('status', 'in_production')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Completed</div>
            <div class="bx-stat-value text-success">{{ $orders->where('status', 'completed')->count() }}</div>
        </div>
    </div>

    <!-- ─── FILTERS ─── -->
    <div class="bx-filters">
        <div class="bx-filters-left">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by order number or customer..."
                       class="bx-search-input" />
            </div>
            <select wire:model.live="statusFilter" class="bx-select">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="in_production">In Production</option>
                <option value="completed">Completed</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
        <div class="bx-filters-right">
            <!-- Optional: Add export or other actions -->
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('order_number')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Order Number
                            @if($sortField === 'order_number')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('customer_id')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Customer
                            @if($sortField === 'customer_id')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('status')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Status
                            @if($sortField === 'status')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('requested_date')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Requested Date
                            @if($sortField === 'requested_date')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="hidden md:table-cell">Production Dates</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <div class="bx-order-number">#{{ $order->order_number }}</div>
                            </td>
                            <td>
                                <div class="bx-customer-name">{{ $order->customer->name }}</div>
                                @if($order->customer->code)
                                    <div class="bx-customer-code">{{ $order->customer->code }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bx-badge-warning', 'icon' => '⏳'],
                                        'approved' => ['class' => 'bx-badge-info', 'icon' => '✅'],
                                        'in_production' => ['class' => 'bx-badge-primary', 'icon' => '🏭'],
                                        'completed' => ['class' => 'bx-badge-success', 'icon' => '✔️'],
                                        'delivered' => ['class' => 'bx-badge-secondary', 'icon' => '🚚'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['class' => 'bx-badge-gray', 'icon' => '📋'];
                                @endphp
                                <span class="bx-badge {{ $config['class'] }}">
                                    {{ $config['icon'] }} {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td>{{ $order->requested_date->format('M d, Y') }}</td>
                            <td class="hidden md:table-cell">
                                @if($order->production_start_date)
                                    <div class="bx-date-badge bx-date-badge-start">
                                        <span class="bx-date-icon">▶</span>
                                        Started: {{ $order->production_start_date->format('M d, Y') }}
                                    </div>
                                @endif
                                @if($order->production_end_date)
                                    <div class="bx-date-badge bx-date-badge-end">
                                        <span class="bx-date-icon">■</span>
                                        Completed: {{ $order->production_end_date->format('M d, Y') }}
                                    </div>
                                @endif
                                @if(!$order->production_start_date && !$order->production_end_date)
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="bx-actions">
                                    @if($order->status === 'approved')
                                        <button wire:click="markAsInProduction({{ $order->id }})"
                                                wire:confirm="Mark this order as in production?"
                                                wire:loading.attr="disabled"
                                                class="bx-btn bx-btn-primary bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            Start Production
                                        </button>
                                    @endif

                                    @if($order->status === 'in_production')
                                        <button wire:click="markAsCompleted({{ $order->id }})"
                                                wire:confirm="Mark this order as completed? This will notify the sales team."
                                                wire:loading.attr="disabled"
                                                class="bx-btn bx-btn-success bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Mark Completed
                                        </button>
                                    @endif

                                    @if($order->status === 'completed')
                                        <span class="bx-badge bx-badge-success bx-badge-lg">
                                            <span class="bx-badge-dot bx-badge-dot-green"></span>
                                            Ready for Delivery
                                        </span>
                                    @endif

                                    @if($order->status === 'delivered')
                                        <span class="bx-badge bx-badge-info bx-badge-lg">
                                            <span class="bx-badge-dot bx-badge-dot-blue"></span>
                                            Delivered
                                        </span>
                                    @endif

                                    @if($order->status === 'pending')
                                        <span class="bx-badge bx-badge-warning bx-badge-lg">
                                            <span class="bx-badge-dot bx-badge-dot-amber"></span>
                                            Awaiting Approval
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <h3>No production orders found</h3>
                                    <p>Try adjusting your search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($orders->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $orders->firstItem() ?? 0 }}</strong>
                to <strong>{{ $orders->lastItem() ?? 0 }}</strong>
                of <strong>{{ $orders->total() }}</strong> orders
            </div>
            <div class="bx-pagination">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

    <!-- ─── FLASH MESSAGES ─── -->
    @if (session()->has('message'))
        <div class="bx-flash-message bx-flash-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bx-flash-message bx-flash-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif
</div>
