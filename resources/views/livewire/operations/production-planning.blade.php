<!-- resources/views/livewire/operations/planning.blade.php -->
<div class="bx-page bx-page-planning">
    <!-- ─── HEADER ─── -->
    <div class="bx-header bx-header-planning">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Production Planning
            </h1>
            <p class="bx-header-subtitle">Engineering Execution & Material Control</p>
        </div>
        <div class="bx-header-right">
            <div class="bx-toggle-group">
                <button wire:click="$set('activeFilter', 'active')"
                    class="bx-toggle-btn {{ $activeFilter === 'active' ? 'active' : '' }}">
                    Active
                </button>
                <button wire:click="$set('activeFilter', 'historical')"
                    class="bx-toggle-btn {{ $activeFilter === 'historical' ? 'active' : '' }}">
                    Historical
                </button>
            </div>
            <div class="bx-stats-mini">
                <div class="bx-stats-mini-item">
                    <span class="bx-stats-mini-label">Active Batches</span>
                    <span class="bx-stats-mini-value">{{ $ordersWithDemands->count() }}</span>
                </div>
                <div class="bx-stats-mini-item">
                    <span class="bx-stats-mini-label">Materials Planned</span>
                    <span class="bx-stats-mini-value text-emerald-500">{{ count($globalMaterialSummary) }}</span>
                </div>
            </div>
        </div>
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

    <div class="bx-planning-grid">
        <!-- ─── MAIN AREA ─── -->
        <div class="bx-planning-main">

            @if($viewingOrder)
                <!-- ═══ ORDER DETAIL VIEW ═══ -->
                <div class="bx-detail-view">
                    <!-- Back Navigation -->
                    <div class="bx-detail-nav">
                        <button wire:click="backToList" class="bx-btn bx-btn-secondary bx-btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Queue
                        </button>
                        <div class="bx-detail-status">
                            @php
                                $statusColors = [
                                    'pending' => 'bx-badge-warning',
                                    'pending_production' => 'bx-badge-info',
                                    'approved' => 'bx-badge-success',
                                ];
                                $sc = $statusColors[$viewingOrder->status] ?? 'bx-badge-gray';
                            @endphp
                            <span class="bx-badge {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $viewingOrder->status)) }}</span>
                        </div>
                    </div>

                    <!-- Planning Report -->
                    <div class="bx-card bx-card-planning" id="planning-report">
                        <!-- Report Header -->
                        <div class="bx-planning-header">
                            <div class="bx-planning-header-left">
                                <div class="bx-planning-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="bx-planning-order-title">Order #{{ $viewingOrder->order_number }}</h3>
                                    <div class="bx-planning-order-meta">
                                        <span>{{ $viewingOrder->customer->name ?? 'N/A' }}</span>
                                        <span class="bx-planning-order-date">{{ now()->format('D, d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bx-planning-header-right print:hidden">
                                <button onclick="window.print()" class="bx-btn bx-btn-secondary bx-btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Download PDF
                                </button>
                                @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                    <button wire:click="savePlan" class="bx-btn bx-btn-secondary bx-btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                        </svg>
                                        Save Draft
                                    </button>
                                    <button wire:click="approvePlan" class="bx-btn bx-btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Release to Floor
                                    </button>
                                @else
                                    <span class="bx-badge bx-badge-success bx-badge-lg">✓ Plan Approved & Released</span>
                                @endif
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="bx-planning-schedule">
                            <h4>Production Schedule</h4>
                            <div class="bx-planning-schedule-grid">
                                <div>
                                    <label>Production Line</label>
                                    <select wire:model="productionLineId"
                                            {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                            class="bx-select {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}">
                                        <option value="">Choose Line</option>
                                        @foreach($productionLines as $line)
                                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label>Planned Start</label>
                                    <input type="datetime-local" wire:model="startDate"
                                           {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                           class="bx-input {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}" />
                                </div>
                                <div>
                                    <label class="text-emerald-500">Target End</label>
                                    <input type="datetime-local" wire:model="endDate"
                                           {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                           class="bx-input {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}" />
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 1: Production Demand -->
                        <div class="bx-planning-section">
                            <h4>
                                <span class="bx-planning-section-dot"></span>
                                Production Demand — What to Make
                            </h4>

                            @if($orderItems->isEmpty())
                                <div class="bx-planning-empty">
                                    <p>No order items found for this production order.</p>
                                    <p class="text-gray">Add items via Sales → Orders.</p>
                                </div>
                            @else
                                <div class="bx-planning-table-wrap">
                                    <table class="bx-table bx-table-planning">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">OD / Spec</th>
                                                <th class="text-center">SDR</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Unit Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orderItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="bx-planning-product-name">{{ $item->product->name ?? 'Unknown Product' }}</div>
                                                        @if($item->pn)
                                                            <div class="bx-planning-product-pn">PN: {{ $item->pn }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->od)
                                                            <span class="bx-code">{{ $item->od }} mm</span>
                                                        @else
                                                            <span class="bx-text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->sdr)
                                                            <span class="bx-code bx-code-blue">SDR {{ $item->sdr }}</span>
                                                        @else
                                                            <span class="bx-text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="bx-planning-qty">{{ number_format($item->quantity, 0) }}</span>
                                                        <span class="bx-planning-unit">{{ $item->unit }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="bx-planning-price">{{ number_format($item->unit_price, 2) }}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="bx-planning-total">{{ number_format($item->total_price, 2) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="bx-planning-totals-label">Totals</td>
                                                <td class="text-center">
                                                    <span class="bx-planning-totals-value">{{ number_format($orderItems->sum('quantity'), 0) }}</span>
                                                    <span class="bx-planning-unit">units</span>
                                                </td>
                                                <td></td>
                                                <td class="text-right">
                                                    <span class="bx-planning-totals-value">{{ number_format($orderItems->sum('total_price'), 2) }}</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- SECTION 2: Raw Material Plan -->
                        <div class="bx-planning-section bx-planning-materials">
                            <div class="bx-planning-materials-header">
                                <h4>
                                    <span class="bx-planning-section-dot bx-planning-section-dot-blue"></span>
                                    Raw Material Requirements
                                </h4>
                                @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                    <button wire:click="openAddMaterialModal" class="bx-btn bx-btn-primary bx-btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Raw Material
                                    </button>
                                @endif
                            </div>

                            @if(count($aggregatedMaterialSummary) > 0)
                                <div class="bx-material-grid">
                                    @foreach($aggregatedMaterialSummary as $matId => $summary)
                                        @php
                                            $stockOk = $summary['in_stock'] >= $summary['total_quantity'];
                                            $coverage = $summary['total_quantity'] > 0
                                                ? min(100, ($summary['in_stock'] / $summary['total_quantity']) * 100)
                                                : 0;
                                        @endphp
                                        <div class="bx-material-card {{ $stockOk ? 'bx-material-ok' : 'bx-material-low' }}">
                                            <div class="bx-material-card-header">
                                                <div>
                                                    <span class="bx-material-card-label">Raw Material</span>
                                                    <h5 class="bx-material-card-name">{{ $summary['name'] }}</h5>
                                                </div>
                                                <div class="bx-material-card-actions">
                                                    @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                                        <button wire:click="openEditMaterialModal({{ $summary['id'] }})"
                                                                class="bx-action bx-action-edit" title="Edit">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="deletePlanItem({{ $summary['id'] }})"
                                                                wire:confirm="Remove this material from the plan?"
                                                                class="bx-action bx-action-delete" title="Delete">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if($stockOk)
                                                        <span class="bx-badge bx-badge-success bx-badge-xs">In Stock ✓</span>
                                                    @else
                                                        <span class="bx-badge bx-badge-danger bx-badge-xs">Stock Low ⚠</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="bx-material-card-stats">
                                                <div>
                                                    <span class="bx-material-stat-label">In Stock</span>
                                                    <span class="bx-material-stat-value">{{ number_format($summary['in_stock'], 1) }}</span>
                                                    <span class="bx-material-stat-unit">{{ $summary['unit'] }}</span>
                                                </div>
                                                <div>
                                                    <span class="bx-material-stat-label bx-material-stat-required">Required</span>
                                                    <span class="bx-material-stat-value">{{ number_format($summary['total_quantity'], 1) }}</span>
                                                    <span class="bx-material-stat-unit">{{ $summary['unit'] }}</span>
                                                </div>
                                                <div>
                                                    <span class="bx-material-stat-label {{ $stockOk ? 'bx-material-stat-ok' : 'bx-material-stat-danger' }}">Shortage</span>
                                                    <span class="bx-material-stat-value {{ $stockOk ? 'text-emerald-600' : 'text-red-600' }}">
                                                        {{ $stockOk ? '0.0' : number_format($summary['total_quantity'] - $summary['in_stock'], 1) }}
                                                    </span>
                                                    <span class="bx-material-stat-unit">{{ $summary['unit'] }}</span>
                                                </div>
                                            </div>

                                            <div class="bx-material-progress">
                                                <div class="bx-material-progress-label">
                                                    <span>Stock Coverage</span>
                                                    <span>{{ round($coverage) }}%</span>
                                                </div>
                                                <div class="bx-material-progress-bar">
                                                    <div class="bx-material-progress-fill {{ $stockOk ? 'bx-progress-ok' : 'bx-progress-danger' }}"
                                                         style="width: {{ $coverage }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="bx-material-note">
                                    <div class="bx-material-note-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p>These quantities are planning projections. Daily floor releases are managed by the Plant Manager.</p>
                                </div>
                            @else
                                <div class="bx-planning-empty bx-planning-empty-materials">
                                    <div class="bx-planning-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h5>No Raw Materials Planned Yet</h5>
                                    <p>Click "Add Raw Material" above to plan the materials needed to fulfil this order.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="bx-planning-footer">
                            <div class="bx-planning-footer-item">
                                <span>Authorized Planner</span>
                            </div>
                            <div class="bx-planning-footer-center">
                                SHUMBURU • ERP • {{ now()->year }}
                            </div>
                            <div class="bx-planning-footer-item">
                                <span>Operations Approval</span>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- ═══ LIST VIEW ═══ -->
                <div class="bx-order-grid">
                    @forelse ($ordersWithDemands as $order)
                        @php
                            $isApproved = $order->plan && $order->plan->status === 'approved';
                            $statusColors = [
                                'pending' => 'bx-badge-warning',
                                'pending_production' => 'bx-badge-info',
                                'approved' => 'bx-badge-success',
                                'in_production' => 'bx-badge-primary',
                                'completed' => 'bx-badge-gray',
                            ];
                            $sc2 = $statusColors[$order->status] ?? 'bx-badge-gray';
                        @endphp
                        <div class="bx-order-card {{ $isApproved ? 'bx-order-approved' : '' }}"
                             wire:click="selectOrder({{ $order->id }})">
                            <div class="bx-order-card-header">
                                <div class="bx-order-card-icon {{ $isApproved ? 'bx-order-icon-approved' : '' }}">
                                    #
                                </div>
                                <div>
                                    <h4 class="bx-order-card-title">Order #{{ $order->order_number }}</h4>
                                    <p class="bx-order-card-customer">{{ $order->customer->name ?? 'N/A' }}</p>
                                </div>
                                <div class="bx-order-card-status">
                                    <span class="bx-badge {{ $sc2 }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                </div>
                            </div>

                            <div class="bx-order-card-stats">
                                <div>
                                    <span class="bx-order-stat-label">Items</span>
                                    <span class="bx-order-stat-value">{{ $order->items->count() }}</span>
                                </div>
                                <div>
                                    <span class="bx-order-stat-label">Qty</span>
                                    <span class="bx-order-stat-value">{{ number_format($order->items->sum('quantity'), 0) }}</span>
                                </div>
                                <div>
                                    <span class="bx-order-stat-label">Materials</span>
                                    <span class="bx-order-stat-value {{ $order->plan ? 'text-blue-600' : 'text-gray-400' }}">
                                        {{ $order->plan ? $order->plan->items->count() : '—' }}
                                    </span>
                                </div>
                            </div>

                            <div class="bx-order-card-footer">
                                <div class="bx-order-card-avatars">
                                    <span class="bx-avatar bx-avatar-xs">M</span>
                                    <span class="bx-avatar bx-avatar-xs bx-avatar-blue">P</span>
                                </div>
                                @if($isApproved)
                                    <span class="bx-order-card-action">✓ View Approved Plan →</span>
                                @else
                                    <span class="bx-order-card-action">Plan This Order →</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bx-empty-state bx-empty-state-large">
                            <div class="bx-empty-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h5>Production Queue Empty</h5>
                            <p>Awaiting new production orders from sales.</p>
                        </div>
                    @endforelse
                </div>

                <!-- ─── GLOBAL MATERIAL SUMMARY ─── -->
                <div class="bx-card bx-card-global">
                    <div class="bx-card-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Global Planned Materials
                        </h3>
                        <span class="bx-badge bx-badge-secondary">{{ count($globalMaterialSummary) }} Active Materials</span>
                    </div>
                    <div class="bx-card-body">
                        @if(count($globalMaterialSummary) > 0)
                            <div class="bx-global-material-grid">
                                @foreach($globalMaterialSummary as $summary)
                                    @php $gs = $summary['in_stock'] >= $summary['total_quantity']; @endphp
                                    <div class="bx-global-material-item {{ $gs ? 'bx-global-material-ok' : 'bx-global-material-low' }}">
                                        <div class="bx-global-material-header">
                                            <div>
                                                <span class="bx-global-material-label">Raw Material</span>
                                                <h6 class="bx-global-material-name">{{ $summary['name'] }}</h6>
                                            </div>
                                            <div class="bx-global-material-dot {{ $gs ? 'bx-dot-green' : 'bx-dot-red' }}"></div>
                                        </div>
                                        <div class="bx-global-material-stats">
                                            <div>
                                                <span class="bx-global-stat-label">In Warehouse</span>
                                                <span class="bx-global-stat-value">{{ number_format($summary['in_stock'], 1) }}
                                                    <span class="bx-global-stat-unit">{{ $summary['unit'] }}</span>
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="bx-global-stat-label bx-global-stat-required">Total Required</span>
                                                <span class="bx-global-stat-value bx-global-stat-large">{{ number_format($summary['total_quantity'], 1) }}
                                                    <span class="bx-global-stat-unit">{{ $summary['unit'] }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bx-empty-state">
                                <p>No material requirements planned yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ─── SIDEBAR ─── -->
        <div class="bx-planning-sidebar print:hidden">
            <!-- Downtime Monitor -->
            <div class="bx-card bx-card-downtime">
                <div class="bx-card-header bx-card-header-downtime">
                    <h3>
                        <span class="bx-downtime-dot"></span>
                        Equipment Downtime Log
                    </h3>
                </div>
                <div class="bx-card-body">
                    @forelse($recentDowntime as $record)
                        <div class="bx-downtime-item">
                            <span class="bx-downtime-reason">{{ $record->reason }}</span>
                        </div>
                    @empty
                        <div class="bx-downtime-empty">
                            <div class="bx-downtime-empty-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h5>All Systems Nominal</h5>
                            <p>No downtime logged ✓</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Planning Readiness -->
            <div class="bx-card bx-card-readiness">
                <div class="bx-card-body">
                    <h4>System Readiness</h4>
                    <div class="bx-readiness-stats">
                        <div class="bx-readiness-item">
                            <span>Active Queue</span>
                            <span class="bx-readiness-value">{{ $ordersWithDemands->count() }}</span>
                        </div>
                        <div class="bx-readiness-item">
                            <span>Materials Catalogued</span>
                            <span class="bx-readiness-value bx-readiness-value-blue">{{ count($globalMaterialSummary) }}</span>
                        </div>
                        <div class="bx-readiness-item">
                            <span>Plans Approved</span>
                            <span class="bx-readiness-value bx-readiness-value-green">
                                {{ $ordersWithDemands->where('status', 'approved')->count() }}
                            </span>
                        </div>
                    </div>
                    <p class="bx-readiness-note">Planning precision directly impacts factory floor throughput and overall material wastage.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── ADD/EDIT MATERIAL MODAL ─── -->
    @if($showMaterialModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showMaterialModal', false)">
            <div class="bx-modal bx-modal-material">
                <form wire:submit.prevent="saveMaterialItem">
                    <div class="bx-modal-header">
                        <div class="bx-modal-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3>{{ $editingPlanItemId ? 'Edit Material' : 'Add Raw Material' }}</h3>
                            <p class="bx-modal-subtitle">Plan the quantity required for this order</p>
                        </div>
                        <button type="button" wire:click="$set('showMaterialModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Raw Material</label>
                                    <select wire:model="materialId"
                                            class="bx-select {{ $editingPlanItemId ? 'opacity-60' : '' }}"
                                            {{ $editingPlanItemId ? 'disabled' : '' }}>
                                        <option value="">Select a raw material...</option>
                                        @foreach($rawMaterials as $material)
                                            <option value="{{ $material->id }}">
                                                {{ $material->name }} — {{ $material->unit }} (Stock: {{ number_format($material->quantity, 0) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('materialId')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Quantity Required</label>
                                    <input type="number" wire:model="materialQty"
                                           class="bx-input bx-input-lg"
                                           step="0.01" min="0.01" placeholder="0.0" />
                                    @error('materialQty')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                    <p class="bx-form-hint">
                                        ⚠️ These quantities represent total order requirements. If this material already exists in the plan, quantities will be merged.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showMaterialModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            {{ $editingPlanItemId ? 'Update Material' : 'Add to Plan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
