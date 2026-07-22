<!-- resources/views/livewire/production/manager-dashboard.blade.php -->
<div class="bx-page bx-page-plant-manager">
    <!-- ─── HEADER ─── -->
    <div class="bx-header bx-header-plant">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Plant Manager Dashboard
            </h1>
            <p class="bx-header-subtitle">
                <span class="bx-status-dot bx-status-dot-green"></span>
                Operations Status: Operational Phase {{ now()->format('H:i') }}
            </p>
        </div>
        <div class="bx-header-right">
            <div class="bx-tabs-nav">
                <button wire:click="$set('activeTab', 'plans')"
                    class="bx-tab-nav {{ $activeTab === 'plans' ? 'active' : '' }}">
                    <span class="bx-tab-nav-icon">📊</span>
                    Batch Queue
                    <span class="bx-tab-nav-badge">{{ $plannedProductionRequests->count() }}</span>
                </button>
                <button wire:click="$set('activeTab', 'warehouse')"
                    class="bx-tab-nav {{ $activeTab === 'warehouse' ? 'active' : '' }}">
                    <span class="bx-tab-nav-icon">📦</span>
                    Supply
                    <span class="bx-tab-nav-badge">{{ $pendingWarehouseRequests->count() }}</span>
                </button>
                <button wire:click="$set('activeTab', 'production')"
                    class="bx-tab-nav {{ $activeTab === 'production' ? 'active' : '' }}">
                    <span class="bx-tab-nav-icon">🏭</span>
                    Running
                    <span class="bx-tab-nav-badge">{{ $inProgressRequests->count() }}</span>
                </button>
                <button wire:click="$set('activeTab', 'completed')"
                    class="bx-tab-nav bx-tab-nav-icon-only {{ $activeTab === 'completed' ? 'active' : '' }}" title="Completed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session()->has('success'))
        <div class="bx-alert bx-alert-dark">
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

    <div class="bx-plant-grid">
        <!-- ─── MAIN CONTENT ─── -->
        <div class="bx-plant-main">

            <!-- ═══ PLAN DETAIL VIEW ═══ -->
            @if($activePlanRequestId && $activePlanRequest)
                <div class="bx-detail-view">
                    <div class="bx-detail-nav">
                        <button wire:click="backToPlans" class="bx-btn bx-btn-secondary bx-btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Operations Dashboard
                        </button>
                    </div>

                    <div class="bx-card bx-card-plant-detail" id="production-report">
                        <!-- Detail Header -->
                        <div class="bx-plant-detail-header">
                            <div class="bx-plant-detail-header-left">
                                <div class="bx-plant-detail-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="bx-plant-detail-title">BATCH #{{ $activePlanRequest->order_number }}</h3>
                                    <p class="bx-plant-detail-subtitle">Operational Specification & Log Profile</p>
                                </div>
                            </div>
                            <div class="bx-plant-detail-header-right">
                                <button onclick="window.print()" class="bx-btn bx-btn-secondary bx-btn-sm print:hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download PDF
                                </button>
                                <div class="bx-plant-volume">
                                    <span class="bx-plant-volume-label">Order Volume</span>
                                    <span class="bx-plant-volume-value">{{ number_format($activePlanRequest->total_quantity, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Content -->
                        <div class="bx-plant-detail-body">
                            @include('livewire.production.plan-detail-content', ['activePlanRequest' => $activePlanRequest])
                        </div>
                    </div>
                </div>

            @else
                <!-- ═══ PLANS TAB ═══ -->
                @if($activeTab === 'plans')
                    <div class="bx-plant-grid-cards">
                        @forelse($plannedProductionRequests as $order)
                            <div class="bx-plant-card" wire:click="selectPlan({{ $order->id }})">
                                <div class="bx-plant-card-header">
                                    <div>
                                        <h4 class="bx-plant-card-title">BATCH #{{ $order->order_number }}</h4>
                                        <div class="bx-plant-card-meta">
                                            <span>{{ $order->customer->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <span class="bx-badge bx-badge-primary">{{ ucfirst($order->status) }}</span>
                                </div>

                                <div class="bx-plant-card-stats">
                                    <div>
                                        <span class="bx-plant-stat-label">Target Output</span>
                                        <span class="bx-plant-stat-value">{{ $order->total_quantity }}
                                            <span class="bx-plant-stat-unit">UNITS</span>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="bx-plant-stat-label">Batch Load</span>
                                        <span class="bx-plant-stat-value bx-plant-stat-value-blue">
                                            📋 {{ $order->plan?->items?->count() ?? 0 }} Designs
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $pendingCount = $order->plan ? \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'pending')->count() : 0;
                                @endphp
                                @if($pendingCount > 0)
                                    <div class="bx-plant-card-warning">
                                        ⚠️ {{ $pendingCount }} Unprocessed Supply Requests
                                    </div>
                                @endif

                                <div class="bx-plant-card-footer">
                                    <div class="bx-plant-card-line">
                                        <div class="bx-plant-card-line-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        @if($order->plan && $order->plan->productionLine)
                                            <span class="bx-plant-card-line-name">{{ $order->plan->productionLine->name }}</span>
                                        @endif
                                    </div>
                                    <span class="bx-plant-card-action">Review Details →</span>
                                </div>
                            </div>
                        @empty
                            <div class="bx-empty-state bx-empty-state-large bx-empty-state-full">
                                <div class="bx-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h5>Queue is Static</h5>
                                <p>No approved plans awaiting operational review.</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            @endif

            <!-- ═══ SUPPLY TAB ═══ -->
            @if($activeTab === 'warehouse' && !$activePlanRequestId)
                <div class="bx-plant-supply">
                    <!-- Pending Releases -->
                    <div class="bx-card bx-card-amber">
                        <div class="bx-card-header bx-card-header-amber">
                            <div>
                                <h3>Active Releases</h3>
                                <p class="bx-card-subtitle">Batches sent to warehouse queue today</p>
                            </div>
                            <span class="bx-badge bx-badge-amber">{{ $pendingWarehouseRequests->count() }} PENDING</span>
                        </div>
                        <div class="bx-card-body bx-card-body-divided">
                            @forelse($pendingWarehouseRequests as $request)
                                <div class="bx-supply-item">
                                    <div class="bx-supply-item-left">
                                        <div class="bx-supply-item-icon">📦</div>
                                        <div>
                                            <div class="bx-supply-item-name">{{ $request->rawMaterial->name }}</div>
                                            <p class="bx-supply-item-target">Target: Batch #{{ $request->productionPlan->productionOrder->order_number ?? '---' }}</p>
                                        </div>
                                    </div>
                                    <div class="bx-supply-item-right">
                                        <div class="bx-supply-item-qty">{{ number_format($request->quantity, 1) }}
                                            <span class="bx-supply-item-unit">{{ $request->rawMaterial->unit }}</span>
                                        </div>
                                        <span class="bx-badge bx-badge-gray bx-badge-xs">Awaiting Load</span>
                                    </div>
                                </div>
                            @empty
                                <div class="bx-empty-state bx-empty-state-sm">
                                    <p>Supply Queue Clear</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Confirmed Deliveries -->
                    <div class="bx-card bx-card-green">
                        <div class="bx-card-header bx-card-header-green">
                            <div>
                                <h3>Factory Floor Stocked</h3>
                                <p class="bx-card-subtitle">Confirmed issuance from terminal today</p>
                            </div>
                        </div>
                        <div class="bx-card-body bx-card-body-compact">
                            @forelse($issuedMaterialsToday as $material)
                                <div class="bx-delivery-item">
                                    <div class="bx-delivery-item-left">
                                        <div class="bx-delivery-item-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="bx-delivery-item-name">{{ $material->rawMaterial->name }}</div>
                                            <p class="bx-delivery-item-time">Verified Load @ {{ $material->updated_at->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="bx-delivery-item-qty">{{ number_format($material->quantity, 1) }}
                                        <span class="bx-delivery-item-unit">{{ $material->rawMaterial->unit }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="bx-empty-state bx-empty-state-sm">
                                    <p>No inbound stock confirmed</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            <!-- ═══ RUNNING TAB ═══ -->
            @if($activeTab === 'production' && !$activePlanRequestId)
                <div class="bx-plant-running">
                    @forelse($inProgressRequests as $order)
                        @php
                            if ($order->plan) {
                                $issued = \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'issued')->sum('quantity');
                                $consumed = \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'consumed')->sum('quantity');
                                $matProgress = $issued > 0 ? min(100, ($consumed / $issued) * 100) : 0;
                            } else {
                                $issued = 0;
                                $consumed = 0;
                                $matProgress = 0;
                            }
                        @endphp
                        <div class="bx-running-card" wire:click="selectPlan({{ $order->id }})">
                            <div class="bx-running-card-header">
                                <div class="bx-running-card-header-left">
                                    <div class="bx-running-card-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="bx-running-card-title">BATCH #{{ $order->order_number }}</h4>
                                        <p class="bx-running-card-status">
                                            <span class="bx-status-dot bx-status-dot-red"></span>
                                            Live Execution Phase
                                        </p>
                                    </div>
                                </div>
                                <div class="bx-running-card-line">
                                    <span class="bx-badge bx-badge-dark">{{ $order->plan?->productionLine->name ?? 'Line Primary' }}</span>
                                </div>
                            </div>

                            <div class="bx-running-card-stats">
                                <div>
                                    <span class="bx-running-stat-label">Floor Supply</span>
                                    <span class="bx-running-stat-value">{{ number_format($issued, 1) }} kg received</span>
                                    <div class="bx-running-stat-progress">
                                        <span class="bx-running-stat-progress-label">{{ round($matProgress) }}%</span>
                                        <div class="bx-progress-bar">
                                            <div class="bx-progress-fill bx-progress-fill-blue" style="width: {{ $matProgress }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="bx-running-stat-label">Runtime Clock</span>
                                    <span class="bx-running-stat-value">{{ $order->plan?->start_date?->diffForHumans() ?? 'Pending Initial' }}</span>
                                    <div class="bx-running-stat-progress">
                                        <span class="bx-running-stat-progress-label bx-running-stat-progress-label-green">Active</span>
                                        <div class="bx-progress-bar">
                                            <div class="bx-progress-fill bx-progress-fill-green bx-progress-animated"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button wire:click.stop="openCompleteForm({{ $order->id }})"
                                    class="bx-btn bx-btn-success bx-btn-block">
                                ✓ Signal Completion & Log Result
                            </button>
                        </div>
                    @empty
                        <div class="bx-empty-state bx-empty-state-large bx-empty-state-full bx-empty-state-factory">
                            <div class="bx-empty-icon bx-empty-icon-large">🏭</div>
                            <h5>Factory Floor Secondary Phase</h5>
                            <p>No live runs currently initialized on the line controllers.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            <!-- ═══ COMPLETED TAB ═══ -->
            @if($activeTab === 'completed' && !$activePlanRequestId)
                <div class="bx-card bx-card-completed">
                    <div class="bx-card-header">
                        <div>
                            <h3>Post-Operational Log</h3>
                            <p class="bx-card-subtitle">Archive of verified completions (Shift Historical)</p>
                        </div>
                    </div>
                    <div class="bx-card-body bx-card-body-divided">
                        @forelse($completedRequests as $order)
                            <div class="bx-completed-item" wire:click="selectPlan({{ $order->id }})">
                                <div class="bx-completed-item-left">
                                    <div class="bx-completed-item-check">✓</div>
                                    <div>
                                        <div class="bx-completed-item-name">Batch #{{ $order->order_number }}</div>
                                        <p class="bx-completed-item-date">Verified Resolution: {{ $order->updated_at->format('d M • H:i') }}</p>
                                    </div>
                                </div>
                                <div class="bx-completed-item-right">
                                    <div class="bx-completed-item-qty">{{ number_format($order->total_quantity, 0) }}
                                        <span class="bx-completed-item-unit">Units</span>
                                    </div>
                                    <svg class="bx-completed-item-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        @empty
                            <div class="bx-empty-state bx-empty-state-sm">
                                <p>Archival Ledger Empty</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- ─── SIDEBAR ─── -->
        <div class="bx-plant-sidebar print:hidden">
            <!-- Metrics Console -->
            <div class="bx-card bx-card-metrics">
                <h3 class="bx-metrics-title">Console Metrics</h3>
                <div class="bx-metrics-items">
                    <div class="bx-metrics-item">
                        <span class="bx-metrics-label">Inbound Queue</span>
                        <span class="bx-metrics-value bx-metrics-value-amber">{{ $pendingWarehouseRequests->count() }}</span>
                    </div>
                    <div class="bx-metrics-item">
                        <span class="bx-metrics-label">Floor Activity</span>
                        <span class="bx-metrics-value bx-metrics-value-blue">{{ $inProgressRequests->count() }}</span>
                    </div>
                </div>
                <div class="bx-metrics-total">
                    <div>
                        <span class="bx-metrics-total-label">Stock Issued (24H)</span>
                        <span class="bx-metrics-total-value">{{ number_format($issuedMaterialsToday->sum('quantity'), 0) }}
                            <span class="bx-metrics-total-unit">kg</span>
                        </span>
                    </div>
                    <div class="bx-metrics-total-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Shift Security -->
            <div class="bx-card bx-card-security">
                <h4 class="bx-security-title">Shift Security</h4>
                <div class="bx-security-items">
                    <div class="bx-security-item">
                        <span class="bx-security-dot {{ $plannedProductionRequests->count() > 0 ? 'bx-security-dot-green' : 'bx-security-dot-gray' }}"></span>
                        <span class="bx-security-label">Approved Plans: {{ $plannedProductionRequests->count() }}</span>
                    </div>
                    <div class="bx-security-item">
                        <span class="bx-security-dot {{ $inProgressRequests->count() > 0 ? 'bx-security-dot-blue' : 'bx-security-dot-gray' }}"></span>
                        <span class="bx-security-label">Floor Pulse: {{ $inProgressRequests->count() }} Active</span>
                    </div>
                </div>
                <div class="bx-security-quote">
                    "Precision is not just a metric, it's our core operational principle."
                    <br><br>
                    — Operational Directive
                </div>
            </div>
        </div>
    </div>

    <!-- ─── WAREHOUSE REQUEST MODAL ─── -->
    @if($showWarehouseRequestForm)
        <div class="bx-modal-overlay" wire:click.self="cancelWarehouseRequest">
            <div class="bx-modal bx-modal-warehouse">
                <form wire:submit.prevent="sendWarehouseRequest">
                    <div class="bx-modal-header">
                        <div class="bx-modal-header-icon bx-modal-header-icon-indigo">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Shift Release</h3>
                            <p class="bx-modal-subtitle">Factory Floor Daily Batch Allocation</p>
                        </div>
                        <button type="button" wire:click="cancelWarehouseRequest" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        @php $selectedMaterial = \App\Models\RawMaterial::find($warehouseRequestMaterialId); @endphp

                        <div class="bx-warehouse-material">
                            <span class="bx-warehouse-material-label">Input Catalog Entry</span>
                            <div class="bx-warehouse-material-name">{{ $selectedMaterial?->name ?? 'Unidentified Material' }}</div>
                            <div class="bx-warehouse-material-stock">
                                <span class="bx-status-dot bx-status-dot-green"></span>
                                Warehouse Depth: {{ number_format($selectedMaterial?->quantity ?? 0, 1) }}
                                {{ $selectedMaterial?->unit ?? 'kg' }}
                            </div>
                        </div>

                        <div class="bx-form-group">
                            <div class="bx-form-label-row">
                                <label class="bx-form-label">Quantum for Shift</label>
                                <span class="bx-form-hint-text">{{ $selectedMaterial?->unit ?? 'kg' }} Units</span>
                            </div>
                            <input type="number" wire:model="warehouseRequestQty"
                                   class="bx-input bx-input-lg bx-input-centered"
                                   step="0.1" min="0.01" placeholder="0.0" />
                            @error('warehouseRequestQty')
                                <span class="bx-error">{{ $message }}</span>
                            @enderror
                            <div class="bx-form-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p>This allocation is subtracted from the planned totals. Avoid over-requesting to prevent floor congestion.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="cancelWarehouseRequest" class="bx-btn bx-btn-secondary">Abort</button>
                        <button type="submit" class="bx-btn bx-btn-dark">
                            Release Batch to Supply →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── COMPLETION MODAL ─── -->
    @if($showProductionForm)
        <div class="bx-modal-overlay" wire:click.self="cancelProduction">
            <div class="bx-modal bx-modal-completion">
                <form wire:submit.prevent="completeProduction">
                    <div class="bx-modal-header">
                        <div class="bx-modal-header-icon bx-modal-header-icon-green">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Record Exit</h3>
                            <p class="bx-modal-subtitle">Closing Factory Run & Logging Output</p>
                        </div>
                        <button type="button" wire:click="cancelProduction" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form-group">
                            <div class="bx-form-label-row">
                                <label class="bx-form-label">Verified Net Output</label>
                                <span class="bx-form-hint-text bx-form-hint-text-green">UNITS DISPATCH READY</span>
                            </div>
                            <input type="number" wire:model="actualProduced"
                                   class="bx-input bx-input-lg bx-input-centered bx-input-green"
                                   step="0.1" min="0" placeholder="0" />
                            @error('actualProduced')
                                <span class="bx-error">{{ $message }}</span>
                            @enderror
                            @if($activeRequestId)
                                @php $activeOrderObj = \App\Models\ProductionOrder::find($activeRequestId); @endphp
                                <div class="bx-completion-target">
                                    <span class="bx-completion-target-label">Specified Lab Target</span>
                                    <span class="bx-completion-target-value">{{ number_format($activeOrderObj->total_quantity ?? 0, 0) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="bx-form-group">
                            <label class="bx-form-label">Post-Operational Review / QC Remarks</label>
                            <textarea wire:model="productionNotes"
                                      class="bx-input bx-textarea"
                                      rows="4"
                                      placeholder="Annotate shift performance, anomalies or quality pass details..."></textarea>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="cancelProduction" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-success">
                            ✓ Verify & Finalize Archive
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
