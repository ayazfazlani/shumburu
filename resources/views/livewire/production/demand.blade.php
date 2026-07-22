<!-- resources/views/livewire/operations/demand-control.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Productions Authorization Center
        </h1>
        <p class="bx-header-subtitle">Control point for all production authorizations</p>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session()->has('success'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bx-alert bx-alert-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Orders</div>
            <div class="bx-stat-value">{{ $ordersWithStockDemands->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Pending Items</div>
            <div class="bx-stat-value text-warning">
                {{ $ordersWithStockDemands->sum(function($order) {
                    return $order->orderItems()->whereHas('stockDemands', function($q) {
                        $q->where('status', 'pending');
                    })->count();
                }) }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Manual Demands</div>
            <div class="bx-stat-value text-blue">{{ $individualFgDemands->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Authorized</div>
            <div class="bx-stat-value text-green">
                {{ $ordersWithStockDemands->sum(function($order) {
                    return $order->orderItems()->whereHas('stockDemands', function($q) {
                        $q->where('status', 'authorized');
                    })->count();
                }) }}
            </div>
        </div>
    </div>

    @if($viewingOrder)
        <!-- ─── DETAIL VIEW ─── -->
        <div class="bx-detail-view">
            <div class="bx-detail-header">
                <button wire:click="backToList" class="bx-btn bx-btn-secondary bx-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Orders
                </button>
                <div class="bx-detail-badge">
                    <span class="bx-detail-label">Order #{{ $viewingOrder->order_number }}</span>
                    <span class="bx-badge bx-badge-secondary">{{ $viewingOrder->customer->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="bx-card">
                <div class="bx-card-header">
                    <h3>Authorize Items for Order: #{{ $viewingOrder->order_number }}</h3>
                </div>
                <div class="bx-table-wrap">
                    <div class="bx-table-scroll">
                        <table class="bx-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity Needed</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($selectedOrderFgDemands as $demand)
                                    <tr>
                                        <td>
                                            <div class="bx-product-cell">
                                                <span class="bx-product-name">{{ $demand->product->name }}</span>
                                                <span class="bx-product-code">{{ $demand->product->code }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="bx-quantity-primary">{{ number_format($demand->quantity, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <button wire:click="authorizeProduction({{ $demand->id }})"
                                                    class="bx-btn bx-btn-primary bx-btn-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Authorize Production
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="bx-empty">
                                            <div class="bx-empty-content">
                                                <div class="bx-empty-icon">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <h3>No pending demands for this order</h3>
                                                <p>All production items have been authorized.</p>
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
    @else
        <!-- ─── ORDER LIST ─── -->
        <div class="bx-card mb-6">
            <div class="bx-card-header">
                <h3>Production Orders Requiring Authorization</h3>
                <span class="bx-badge bx-badge-secondary">{{ $ordersWithStockDemands->count() }} Orders</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th class="text-center">Products to Authorize</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordersWithStockDemands as $order)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">#{{ $order->order_number }}</span>
                                    </td>
                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="bx-badge bx-badge-info">
                                            {{ $order->orderItems()->whereHas('stockDemands', function($q) {
                                                $q->where('status', 'pending');
                                            })->count() }} items
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <button wire:click="selectOrder({{ $order->id }})"
                                                class="bx-btn bx-btn-primary bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View & Authorize
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3>No pending orders require authorization</h3>
                                            <p>All production orders have been authorized.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ─── MANUAL DEMANDS ─── -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Manual / Individual Demands
                </h3>
                <span class="bx-badge bx-badge-secondary">{{ $individualFgDemands->count() }} Demands</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th>Requested By</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($individualFgDemands as $demand)
                                <tr>
                                    <td>
                                        <div class="bx-product-cell">
                                            <span class="bx-product-name">{{ $demand->product->name }}</span>
                                            <span class="bx-product-code">{{ $demand->product->code }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="bx-quantity-primary">{{ number_format($demand->quantity, 2) }}</span>
                                    </td>
                                    <td>{{ $demand->requestedBy->name ?? 'System' }}</td>
                                    <td class="text-right">
                                        <button wire:click="authorizeProduction({{ $demand->id }})"
                                                class="bx-btn bx-btn-primary bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Authorize
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </div>
                                            <h3>No manual demands</h3>
                                            <p>All individual production requests have been processed.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
