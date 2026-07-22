<!-- resources/views/livewire/warehouse/demand-control.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Warehouse Authorization Center
        </h1>
        <p class="bx-header-subtitle">Control point for all raw material authorizations</p>
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
            <div class="bx-stat-label">Total Requests</div>
            <div class="bx-stat-value">{{ $rmRequests->count() + $rmDemands->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Pending Stock Out</div>
            <div class="bx-stat-value text-blue">{{ $rmRequests->where('status', 'pending')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Awaiting Purchase</div>
            <div class="bx-stat-value text-warning">{{ $rmDemands->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Completed</div>
            <div class="bx-stat-value text-green">{{ $rmRequests->where('status', 'completed')->count() }}</div>
        </div>
    </div>

    <!-- ─── PRODUCTION MATERIAL DEMANDS ─── -->
    <div class="bx-card bx-card-primary mb-6">
        <div class="bx-card-header bx-card-header-primary">
            <h3>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Production Material Demands (Issue Stock)
            </h3>
            <span class="bx-badge bx-badge-primary">{{ $rmRequests->count() }} Total Items</span>
        </div>
        <div class="bx-card-body">
            @php
                $groupedRequests = $rmRequests->groupBy('plan_reference_id');
            @endphp

            @forelse ($groupedRequests as $planRefId => $requests)
                @php
                    $firstReq = $requests->first();
                    $orderNumber = $firstReq->order_number;
                @endphp
                <div class="bx-demand-group">
                    <div class="bx-demand-header">
                        <div>
                            <span class="bx-demand-label">Order #{{ $orderNumber }} / Product:</span>
                            <h4 class="bx-demand-title">{{ $firstReq->product_name }}</h4>
                        </div>
                        <span class="bx-code">Planning Ref #{{ $planRefId }}</span>
                    </div>
                    <div class="bx-table-wrap">
                        <div class="bx-table-scroll">
                            <table class="bx-table">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Current Stock</th>
                                        <th>Requested Qty</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $req)
                                        @php $stockOk = $req->rawMaterial->quantity >= $req->quantity; @endphp
                                        <tr>
                                            <td class="font-medium">{{ $req->rawMaterial->name }}</td>
                                            <td>
                                                <span class="bx-stock-badge {{ $stockOk ? 'bx-stock-ok' : 'bx-stock-low' }}">
                                                    {{ number_format($req->rawMaterial->quantity, 2) }} {{ $req->rawMaterial->unit }}
                                                </span>
                                            </td>
                                            <td class="font-bold text-blue-600">
                                                {{ number_format($req->quantity, 2) }}
                                            </td>
                                            <td>
                                                <div class="bx-actions">
                                                    @if($req->status === 'pending')
                                                        @if($stockOk)
                                                            <button wire:click="stockOutMaterial({{ $req->id }})"
                                                                    class="bx-btn bx-btn-success bx-btn-sm">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                                Stock Out
                                                            </button>
                                                        @else
                                                            <button wire:click="forwardToProcurement({{ $req->id }})"
                                                                    class="bx-btn bx-btn-warning bx-btn-sm">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                </svg>
                                                                Shortage: Forward to PR
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="bx-badge bx-badge-gray">{{ $req->status }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bx-empty">
                    <div class="bx-empty-content">
                        <div class="bx-empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3>No material issue requests found</h3>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ─── PURCHASE REQUESTS ─── -->
    <div class="bx-card">
        <div class="bx-card-header">
            <h3>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M3 3l9 9 9-9"/>
                </svg>
                Purchase Requests Awaiting Authorization
            </h3>
            <span class="bx-badge bx-badge-secondary">{{ $rmDemands->count() }} items</span>
        </div>
        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Requested Date</th>
                            <th>Raw Material</th>
                            <th>Current Stock</th>
                            <th>Requested Qty</th>
                            <th>Requested By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rmDemands as $req)
                            <tr>
                                <td class="text-gray text-sm">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="font-medium">{{ $req->rawMaterial->name }}</div>
                                    <div class="text-gray text-xs">{{ $req->rawMaterial->code }}</div>
                                </td>
                                <td>
                                    <span class="bx-code">{{ number_format($req->rawMaterial->quantity, 2) }} {{ $req->rawMaterial->unit }}</span>
                                </td>
                                <td>
                                    <span class="text-lg font-bold text-secondary">{{ number_format($req->quantity, 2) }}</span>
                                </td>
                                <td>{{ $req->requestedBy->name ?? 'Plant' }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="authorizePurchase({{ $req->id }})"
                                                class="bx-btn bx-btn-secondary bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Approve For Finance
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M3 3l9 9 9-9"/>
                                            </svg>
                                        </div>
                                        <h3>No pending purchase requests</h3>
                                        <p>All raw material requisitions have been processed.</p>
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
