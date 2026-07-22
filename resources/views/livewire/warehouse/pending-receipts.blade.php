<!-- resources/views/livewire/warehouse/pending-receipts.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            Goods Receipt Center
        </h1>
        <p class="bx-header-subtitle">Accept incoming materials into the physical warehouse inventory</p>
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
            <div class="bx-stat-label">Total Receipts</div>
            <div class="bx-stat-value">{{ $receipts->count() + $rmReceipts->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">FG Pending</div>
            <div class="bx-stat-value text-blue">{{ $receipts->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">RM Pending</div>
            <div class="bx-stat-value text-emerald-600">{{ $rmReceipts->where('status', 'delivered')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">In Transit</div>
            <div class="bx-stat-value text-warning">{{ $rmReceipts->where('status', '!=', 'delivered')->count() }}</div>
        </div>
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs bx-tabs-primary">
            <button wire:click="setTab('fg')"
                    class="bx-tab {{ $activeTab === 'fg' ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ① Production Receipts (FG)
                <span class="bx-tab-badge">{{ $receipts->count() }}</span>
            </button>
            <button wire:click="setTab('rm')"
                    class="bx-tab {{ $activeTab === 'rm' ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                ② Supplier Receipts (RM)
                <span class="bx-tab-badge">{{ $rmReceipts->where('status', 'delivered')->count() }}</span>
            </button>
        </div>
    </div>

    <!-- ─── FINISHED GOODS TABLE ─── -->
    @if($activeTab === 'fg')
        <div class="bx-tab-content active">
            <div class="bx-card">
                <div class="bx-card-header">
                    <h3>Pending FG Arrivals from Production</h3>
                    <span class="bx-badge bx-badge-secondary">{{ $receipts->count() }} Pending</span>
                </div>
                <div class="bx-table-wrap">
                    <div class="bx-table-scroll">
                        <table class="bx-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Batch #</th>
                                    <th class="text-center">Produced Qty</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $receipt)
                                    <tr>
                                        <td class="text-gray text-sm font-bold">{{ $receipt->production_date->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="bx-product-cell">
                                                <span class="bx-product-name">{{ $receipt->product->name }}</span>
                                                <span class="bx-product-code">{{ $receipt->product->code }}</span>
                                            </div>
                                        </td>
                                        <td><span class="bx-code">{{ $receipt->batch_number }}</span></td>
                                        <td class="text-center">
                                            <span class="bx-quantity-primary">{{ number_format($receipt->quantity, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <button wire:click="openConfirmModal({{ $receipt->id }})"
                                                    class="bx-btn bx-btn-primary bx-btn-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Verify & Accept
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="bx-empty">
                                            <div class="bx-empty-content">
                                                <div class="bx-empty-icon">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <h3>No pending production receipts</h3>
                                                <p>All finished goods have been received into inventory.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($receipts->hasPages())
                    <div class="bx-pagination-wrap">
                        {{ $receipts->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ─── RAW MATERIAL TABLE ─── -->
    @if($activeTab === 'rm')
        <div class="bx-tab-content active">
            <div class="bx-card bx-card-success">
                <div class="bx-card-header bx-card-header-success">
                    <h3>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Pending Supplier Shipments (PO)
                    </h3>
                    <span class="bx-badge bx-badge-success">Finance Approved → Warehouse Verified</span>
                </div>
                <div class="bx-table-wrap">
                    <div class="bx-table-scroll">
                        <table class="bx-table">
                            <thead>
                                <tr>
                                    <th>PO / Status</th>
                                    <th>Material Details</th>
                                    <th>Supplier</th>
                                    <th class="text-center">Expected Qty</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rmReceipts as $pr)
                                    <tr>
                                        <td>
                                            <div class="bx-po-cell">
                                                <span class="bx-po-number">#{{ $pr->po_number }}</span>
                                                @if($pr->status === 'delivered')
                                                    <span class="bx-badge bx-badge-success bx-badge-xs">
                                                        <span class="bx-badge-dot bx-badge-dot-green"></span>
                                                        At Gate (Delivered)
                                                    </span>
                                                @else
                                                    <span class="bx-badge bx-badge-warning bx-badge-xs">
                                                        <span class="bx-badge-dot bx-badge-dot-amber"></span>
                                                        In Transit (Approved)
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="bx-material-cell">
                                                <span class="bx-material-name">{{ $pr->rawMaterial->name }}</span>
                                                <span class="bx-material-unit">UNIT: {{ $pr->rawMaterial->unit }}</span>
                                            </div>
                                        </td>
                                        <td class="font-medium text-gray-500 text-sm">{{ $pr->supplier->name ?? 'Unknown Vendor' }}</td>
                                        <td class="text-center">
                                            <span class="bx-quantity-success">{{ number_format($pr->quantity, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            @if($pr->status === 'delivered')
                                                <button wire:click="openRmModal({{ $pr->id }})"
                                                        class="bx-btn bx-btn-success bx-btn-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Confirm GRN
                                                </button>
                                            @else
                                                <span class="bx-badge bx-badge-gray">Pending Delivery</span>
                                            @endif
                                        </td>
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
                                                <h3>No incoming deliveries tracked</h3>
                                                <p>All supplier shipments have been received.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($rmReceipts->hasPages())
                    <div class="bx-pagination-wrap">
                        {{ $rmReceipts->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ─── FG CONFIRM MODAL ─── -->
    @if($showConfirmModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showConfirmModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="confirmReceipt">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Confirm FG Receipt
                        </h3>
                        <button type="button" wire:click="$set('showConfirmModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Physically verifying production output for <strong>{{ $product_name }}</strong>
                        </p>

                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-batch-display">
                                    <span class="bx-batch-label">Batch Reference</span>
                                    <span class="bx-batch-value">{{ $batch_number }}</span>
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Received Quantity</label>
                                    <input type="number" step="0.01"
                                           wire:model="received_quantity"
                                           class="bx-input bx-input-lg"
                                           placeholder="Enter received quantity" />
                                    @error('received_quantity')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Receipt Notes</label>
                                    <textarea wire:model="receipt_notes"
                                              class="bx-input"
                                              rows="3"
                                              placeholder="Shelf location, damage report..."></textarea>
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <label class="bx-checkbox">
                                    <input type="checkbox" wire:model="is_qc_passed" />
                                    <span>Quality Control (QC) Passed</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showConfirmModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Add to Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── RM CONFIRM MODAL ─── -->
    @if($showRmModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showRmModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="confirmRmReceipt">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Raw Material GRN
                        </h3>
                        <button type="button" wire:click="$set('showRmModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Confirm and authorize arrivals of supplier materials
                        </p>

                        <div class="bx-rm-summary">
                            <div class="bx-rm-summary-item">
                                <span class="bx-rm-summary-label">Expected Amount</span>
                                <span class="bx-rm-summary-value">{{ number_format($rm_expected_qty, 2) }}</span>
                            </div>
                            <div class="bx-rm-summary-item">
                                <span class="bx-rm-summary-label">Material</span>
                                <span class="bx-rm-summary-value">{{ $rm_name }}</span>
                            </div>
                        </div>

                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Physical Quantity Received</label>
                                    <input type="number" step="0.001"
                                           wire:model="rm_received_qty"
                                           class="bx-input bx-input-lg bx-input-success"
                                           placeholder="Enter received quantity" />
                                    @error('rm_received_qty')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Internal Notes / Location</label>
                                    <textarea wire:model="rm_notes"
                                              class="bx-input"
                                              rows="3"
                                              placeholder="Any weight variations..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showRmModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Authorize Arrival
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
