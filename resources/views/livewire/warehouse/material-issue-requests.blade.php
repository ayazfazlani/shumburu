<!-- resources/views/livewire/warehouse/material-requests.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Material Fulfillment Center
        </h1>
        <p class="bx-header-subtitle">Review and process raw material requests incoming from planning</p>
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
            <div class="bx-stat-value">{{ $orderWiseRequests->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Plans</div>
            <div class="bx-stat-value text-blue">{{ $orderWiseRequests->sum(function($plans) { return $plans->count(); }) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Pending Fulfillment</div>
            <div class="bx-stat-value text-warning">
                {{ $orderWiseRequests->sum(function($plans) {
                    return $plans->sum(function($requests) {
                        return $requests->where('status', 'pending')->count();
                    });
                }) }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Completed</div>
            <div class="bx-stat-value text-green">
                {{ $orderWiseRequests->sum(function($plans) {
                    return $plans->sum(function($requests) {
                        return $requests->where('status', 'completed')->count();
                    });
                }) }}
            </div>
        </div>
    </div>

    @if ($selectedOrderNumber)
        <!-- ─── DETAIL VIEW: Specific Order Demands ─── -->
        <div class="bx-detail-view">
            <div class="bx-detail-header">
                <button wire:click="backToList" class="bx-btn bx-btn-secondary bx-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Queue
                </button>
                <div class="bx-detail-badge">
                    <span class="bx-detail-label">Order Fulfillment</span>
                    <span class="bx-badge bx-badge-primary">Order #{{ $selectedOrderNumber }}</span>
                </div>
            </div>

            <div class="bx-card">
                <div class="bx-card-header">
                    <h3>Material Requests for Order: #{{ $selectedOrderNumber }}</h3>
                </div>
                <div class="bx-table-wrap">
                    <div class="bx-table-scroll">
                        <table class="bx-table">
                            <thead>
                                <tr>
                                    <th>Material Requirement</th>
                                    <th>Production Plan</th>
                                    <th>Required Qty</th>
                                    <th>Stock Status</th>
                                    <th class="text-right">Fulfillment Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentOrderRequests = $orderWiseRequests[$selectedOrderNumber] ?? collect();
                                @endphp
                                @foreach ($currentOrderRequests as $planId => $materialRequests)
                                    @php
                                        $firstReq = $materialRequests->first();
                                    @endphp
                                    @foreach($materialRequests as $request)
                                        <tr>
                                            <td>
                                                <div class="bx-material-cell">
                                                    <span class="bx-material-name">{{ $request->rawMaterial->name }}</span>
                                                    <span class="bx-material-unit">Unit: {{ $request->rawMaterial->unit }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="bx-plan-cell">
                                                    <span class="bx-plan-id">Plan #{{ $planId }}</span>
                                                    <span class="bx-plan-product">{{ $firstReq->product_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="bx-required-qty">{{ number_format($request->quantity, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($request->rawMaterial->quantity >= $request->quantity)
                                                    <span class="bx-stock-status bx-stock-available">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        In Stock ({{ number_format($request->rawMaterial->quantity, 1) }})
                                                    </span>
                                                @else
                                                    <span class="bx-stock-status bx-stock-shortage">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        Shortage: {{ number_format($request->quantity - $request->rawMaterial->quantity, 1) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="bx-actions">
                                                    @if($request->status === 'pending' || $request->status === 'purchase_raised')
                                                        @if($request->rawMaterial->quantity >= $request->quantity)
                                                            <button wire:click="issueStock({{ $request->id }})"
                                                                    class="bx-btn bx-btn-success bx-btn-sm"
                                                                    onclick="return confirm('Authorize stock issuance for this plan?')">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                                Issue Stock
                                                            </button>
                                                        @else
                                                            <span class="bx-badge bx-badge-danger">Out of Stock</span>
                                                            @if($request->status === 'purchase_raised')
                                                                <span class="bx-badge bx-badge-gray">PR RAISED</span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <span class="bx-badge bx-badge-gray">{{ str_replace('_', ' ', $request->status) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- ─── GRID VIEW: List of Orders ─── -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>Material Fulfillment Queue</h3>
                <span class="bx-badge bx-badge-secondary">{{ $orderWiseRequests->count() }} Orders</span>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Order Identification</th>
                                <th>Customer</th>
                                <th class="text-center">Plan Count</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderWiseRequests as $orderNumber => $plans)
                                <tr>
                                    <td>
                                        <span class="bx-order-number">#{{ $orderNumber }}</span>
                                    </td>
                                    <td class="font-medium text-gray-500 uppercase text-sm">
                                        {{ $plans->first()->first()->customer_name }}
                                    </td>
                                    <td class="text-center">
                                        <span class="bx-code">{{ $plans->count() }} Profiles</span>
                                    </td>
                                    <td class="text-right">
                                        <button wire:click="selectOrder('{{ $orderNumber }}')"
                                                class="bx-btn bx-btn-primary bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Analyze Demands
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No material requests in queue</h3>
                                            <p>All production orders have been fulfilled.</p>
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
