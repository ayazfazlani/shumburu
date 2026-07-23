<!-- resources/views/livewire/sales/orders-overview.blade.php -->
<div class="bx-page bx-page-orders-overview" x-data="{ openDropdown: null }">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Orders Overview
            </h1>
            <p class="bx-header-subtitle">Manage all production orders, payments, and deliveries</p>
        </div>
        <div class="bx-header-right">
            <a href="{{ route('sales.create-order') }}" class="bx-btn bx-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Create New Order</span>
                <span class="sm:hidden">New</span>
            </a>
        </div>
    </div>

    <!-- ─── TOOLBAR ─── -->
    <div class="bx-toolbar">
        <div class="bx-toolbar-left">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..." class="bx-search-input" />
            </div>
            <select wire:model.live="statusFilter" class="bx-select">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="pending_production">Pending Production</option>
                <option value="approved">Approved</option>
                <option value="in_production">In Production</option>
                <option value="completed">Completed</option>
                <option value="delivered">Delivered</option>
            </select>
            <select wire:model.live="customerFilter" class="bx-select">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            <input wire:model.live="dateFilter" type="date" class="bx-input" placeholder="Date" style="max-width: 160px;" />
        </div>
        <div class="bx-toolbar-right">
            <button wire:click="resetFilters" class="bx-btn bx-btn-secondary bx-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="hidden sm:inline">Reset</span>
                <span class="sm:hidden">↻</span>
            </button>
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
            <div class="bx-stat-label">Delivered</div>
            <div class="bx-stat-value text-success">{{ $orders->where('status', 'delivered')->count() }}</div>
        </div>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session()->has('message'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- ─── TABLE ─── -->
    <div class="bx-card">
        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('order_number')" class="cursor-pointer">
                                Order #
                                @if($sortField === 'order_number')
                                    <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('requested_date')" class="cursor-pointer">
                                Date
                                @if($sortField === 'requested_date')
                                    <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th>Customer</th>
                            <th class="hidden sm:table-cell">Items</th>
                            <th class="hidden md:table-cell">Total</th>
                            <th class="hidden lg:table-cell">Paid</th>
                            <th class="hidden xl:table-cell">Progress</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr wire:key="order-{{ $order->id }}">
                                <td>
                                    <span class="bx-order-number">#{{ $order->order_number }}</span>
                                </td>
                                <td>{{ $order->requested_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="bx-customer-name">{{ $order->customer->name }}</div>
                                    <div class="bx-customer-code">{{ $order->customer->code }}</div>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <div class="text-sm">{{ $order->items->count() }} items</div>
                                    <div class="text-xs text-gray-400">{{ $order->items->sum('quantity') }} total qty</div>
                                </td>
                                <td class="hidden md:table-cell font-bold">
                                    ${{ number_format($this->getOrderTotal($order), 2) }}
                                </td>
                                <td class="hidden lg:table-cell font-bold text-success">
                                    ${{ number_format($this->getTotalPaid($order), 2) }}
                                </td>
                                <td class="hidden xl:table-cell">
                                    <div class="bx-progress-wrapper">
                                        <div class="bx-progress-bar">
                                            <div class="bx-progress-fill bx-progress-primary"
                                                 style="width: {{ round($this->getPaymentProgress($order)) }}%"></div>
                                        </div>
                                        <span class="bx-progress-label">{{ round($this->getPaymentProgress($order)) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => 'bx-badge-warning',
                                            'pending_production' => 'bx-badge-warning',
                                            'approved' => 'bx-badge-info',
                                            'in_production' => 'bx-badge-primary',
                                            'completed' => 'bx-badge-success',
                                            'delivered' => 'bx-badge-secondary',
                                        ];
                                        $sc = $statusConfig[$order->status] ?? 'bx-badge-gray';
                                    @endphp
                                    <span class="bx-badge {{ $sc }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="bx-dropdown-actions"
                                         x-data="{ open: false }"
                                         @click.away="open = false"
                                         wire:key="dropdown-{{ $order->id }}">
                                        <button class="bx-dropdown-toggle" @click="open = !open">
                                            <span class="hidden sm:inline">Actions</span>
                                            <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="bx-dropdown-menu" x-show="open" x-transition>
                                            <button wire:click="viewOrderDetails({{ $order->id }})"
                                                    class="bx-dropdown-item"
                                                    @click="open = false">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Details
                                            </button>
                                            <button wire:click="addPayment({{ $order->id }})"
                                                    class="bx-dropdown-item"
                                                    @click="open = false">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                                Add Payment
                                            </button>
                                            <button wire:click="addDelivery({{ $order->id }})"
                                                    class="bx-dropdown-item"
                                                    @click="open = false">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Mark as Delivered
                                            </button>
                                            @if($order->status === 'pending')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'approved')"
                                                        class="bx-dropdown-item"
                                                        @click="open = false">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                            @endif
                                            @if($order->status === 'approved')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'in_production')"
                                                        class="bx-dropdown-item"
                                                        @click="open = false">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    Start Production
                                                </button>
                                            @endif
                                            @if($order->status === 'in_production')
                                                <button wire:click="updateOrderStatus({{ $order->id }}, 'completed')"
                                                        class="bx-dropdown-item"
                                                        @click="open = false">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Mark Complete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h3>No orders found</h3>
                                        <p>Try adjusting your search or filter criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
    </div>

    <!-- ─── ORDER DETAILS MODAL ─── -->
    @if($showOrderDetailsModal)
        <div class="bx-modal-overlay open" wire:click.self="closeOrderDetailsModal" wire:key="order-details-modal-{{ $selectedOrder->id ?? 'new' }}">
            <div class="bx-modal bx-modal-lg">
                <div class="bx-modal-header">
                    <h3>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Order #{{ $selectedOrder->order_number ?? '' }}
                    </h3>
                    <button type="button" wire:click="closeOrderDetailsModal" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bx-modal-body">
                    @if($selectedOrder)
                        <div class="bx-grid-2-1">
                            <div class="bx-card">
                                <div class="bx-card-header">
                                    <h3>Order Information</h3>
                                </div>
                                <div class="bx-card-body">
                                    <div class="bx-form">
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Customer</label>
                                            <div class="bx-input">{{ $selectedOrder->customer->name }}</div>
                                        </div>
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Date</label>
                                            <div class="bx-input">{{ $selectedOrder->requested_date->format('M d, Y') }}</div>
                                        </div>
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Status</label>
                                            <div class="bx-input">
                                                <span class="bx-badge {{ $this->getStatusColor($selectedOrder->status) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Total</label>
                                            <div class="bx-input font-bold text-success">${{ number_format($this->getOrderTotal($selectedOrder), 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bx-card">
                                <div class="bx-card-header">
                                    <h3>Payment Summary</h3>
                                </div>
                                <div class="bx-card-body">
                                    <div class="bx-form">
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Total Paid</label>
                                            <div class="bx-input font-bold text-success">${{ number_format($this->getTotalPaid($selectedOrder), 2) }}</div>
                                        </div>
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Remaining</label>
                                            <div class="bx-input font-bold text-danger">${{ number_format($this->getOrderTotal($selectedOrder) - $this->getTotalPaid($selectedOrder), 2) }}</div>
                                        </div>
                                        <div class="bx-form-group bx-form-full">
                                            <label class="bx-form-label">Progress</label>
                                            <div class="bx-progress-wrapper">
                                                <div class="bx-progress-bar">
                                                    <div class="bx-progress-fill bx-progress-primary"
                                                         style="width: {{ round($this->getPaymentProgress($selectedOrder)) }}%"></div>
                                                </div>
                                                <span class="bx-progress-label">{{ round($this->getPaymentProgress($selectedOrder)) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bx-card mt-4">
                            <div class="bx-card-header">
                                <h3>Order Items</h3>
                                <span class="bx-badge bx-badge-secondary">{{ $selectedOrder->items->count() }}</span>
                            </div>
                            <div class="bx-card-body">
                                <div class="bx-table-wrap">
                                    <table class="bx-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Unit Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedOrder->items as $item)
                                                <tr>
                                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                    <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                                                    <td class="text-center">${{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right font-bold">Total</td>
                                                <td class="text-right font-bold text-success">${{ number_format($selectedOrder->items->sum('total_price'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($selectedOrder->payments->count() > 0)
                            <div class="bx-card mt-4">
                                <div class="bx-card-header">
                                    <h3>Payments</h3>
                                    <span class="bx-badge bx-badge-success">{{ $selectedOrder->payments->count() }}</span>
                                </div>
                                <div class="bx-card-body">
                                    <div class="bx-table-wrap">
                                        <table class="bx-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Method</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedOrder->payments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                        <td class="font-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                                        <td>{{ $payment->notes ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="1" class="font-bold">Total</td>
                                                    <td class="font-bold text-success">${{ number_format($selectedOrder->payments->sum('amount'), 2) }}</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($selectedOrder->deliveries->count() > 0)
                            <div class="bx-card mt-4">
                                <div class="bx-card-header">
                                    <h3>Deliveries</h3>
                                    <span class="bx-badge bx-badge-secondary">{{ $selectedOrder->deliveries->count() }}</span>
                                </div>
                                <div class="bx-card-body">
                                    <div class="bx-table-wrap">
                                        <table class="bx-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Product</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedOrder->deliveries as $delivery)
                                                    <tr>
                                                        <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                                        <td>{{ $delivery->product->name ?? 'N/A' }}</td>
                                                        <td class="text-center">{{ $delivery->quantity }}</td>
                                                        <td class="text-right">${{ number_format($delivery->total_amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right font-bold">Total</td>
                                                    <td class="text-right font-bold text-success">${{ number_format($selectedOrder->deliveries->sum('total_amount'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="bx-modal-footer">
                    <button type="button" wire:click="closeOrderDetailsModal" class="bx-btn bx-btn-secondary">Close</button>
                    @if($selectedOrder && $selectedOrder->status !== 'delivered')
                        <button wire:click="addPayment({{ $selectedOrder->id }})"
                                wire:key="add-payment-btn-{{ $selectedOrder->id }}"
                                class="bx-btn bx-btn-success"
                                wire:loading.attr="disabled">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Add Payment
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ─── PAYMENT MODAL ─── -->
    @if($showPaymentModal)
        <div class="bx-modal-overlay open" wire:click.self="closePaymentModal" wire:key="payment-modal-{{ $selectedOrder->id ?? 'new' }}">
            <div class="bx-modal">
                <form wire:submit.prevent="savePayment" wire:key="payment-form-{{ $selectedOrder->id ?? 'new' }}">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Add Payment
                        </h3>
                        <button type="button" wire:click="closePaymentModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        @if($selectedOrder)
                            <div class="bx-payment-summary">
                                <div><strong>Order:</strong> {{ $selectedOrder->order_number }}</div>
                                <div><strong>Customer:</strong> {{ $selectedOrder->customer->name }}</div>
                                <div><strong>Total Due:</strong> <span class="text-danger">${{ number_format($this->getOrderTotal($selectedOrder) - $this->getTotalPaid($selectedOrder), 2) }}</span></div>
                            </div>
                        @endif

                        <div class="bx-form">
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Amount</label>
                                <input type="number"
                                       wire:model="paymentAmount"
                                       step="0.01"
                                       min="0.01"
                                       class="bx-input @error('paymentAmount') bx-input-error @enderror"
                                       placeholder="0.00" />
                                @error('paymentAmount')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Payment Method</label>
                                <select wire:model="paymentMethod" class="bx-select @error('paymentMethod') bx-input-error @enderror">
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                    <option value="credit_card">Credit Card</option>
                                </select>
                                @error('paymentMethod')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Payment Date</label>
                                <input type="date"
                                       wire:model="paymentDate"
                                       class="bx-input @error('paymentDate') bx-input-error @enderror" />
                                @error('paymentDate')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Notes</label>
                                <textarea wire:model="paymentNotes"
                                          rows="3"
                                          class="bx-input bx-textarea @error('paymentNotes') bx-input-error @enderror"
                                          placeholder="Add payment notes..."></textarea>
                                @error('paymentNotes')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closePaymentModal" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary" wire:loading.attr="disabled" wire:target="savePayment">
                            <span wire:loading.remove wire:target="savePayment">Save Payment</span>
                            <span wire:loading wire:target="savePayment">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELIVERY MODAL ─── -->
    @if($showDeliveryModal)
        <div class="bx-modal-overlay open" wire:click.self="closeDeliveryModal" wire:key="delivery-modal-{{ $selectedOrder->id ?? 'new' }}">
            <div class="bx-modal">
                <form wire:submit.prevent="saveDelivery" wire:key="delivery-form-{{ $selectedOrder->id ?? 'new' }}">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Add Delivery
                        </h3>
                        <button type="button" wire:click="closeDeliveryModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        @if($selectedOrder)
                            <div class="bx-delivery-summary">
                                <div><strong>Order:</strong> {{ $selectedOrder->order_number }}</div>
                                <div><strong>Customer:</strong> {{ $selectedOrder->customer->name }}</div>
                            </div>
                        @endif

                        <div class="bx-form">
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Quantity</label>
                                <input wire:model="deliveryQuantity" type="number" step="0.01" min="0.01"
                                       class="bx-input @error('deliveryQuantity') bx-input-error @enderror"
                                       placeholder="Enter quantity" />
                                @error('deliveryQuantity')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Delivery Date</label>
                                <input wire:model="deliveryDate" type="date"
                                       class="bx-input @error('deliveryDate') bx-input-error @enderror" />
                                @error('deliveryDate')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Notes</label>
                                <textarea wire:model="deliveryNotes" rows="3"
                                          class="bx-input bx-textarea @error('deliveryNotes') bx-input-error @enderror"
                                          placeholder="Delivery notes"></textarea>
                                @error('deliveryNotes')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closeDeliveryModal" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary" wire:loading.attr="disabled" wire:target="saveDelivery">
                            <span wire:loading.remove wire:target="saveDelivery">Save Delivery</span>
                            <span wire:loading wire:target="saveDelivery">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
