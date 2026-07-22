<!-- resources/views/livewire/finance/procurement.blade.php -->
<div class="bx-page bx-page-procurement">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M3 3l9 9 9-9"/>
                </svg>
                Procurement
            </h1>
            <p class="bx-header-subtitle">PR → Approve → PO → GRN → Payment</p>
        </div>
        <div class="bx-header-right">
            <div class="bx-stats-mini">
                <div class="bx-stats-mini-item bx-stats-mini-amber">
                    <span class="bx-stats-mini-label">Pending PRs</span>
                    <span class="bx-stats-mini-value">{{ $stats['pending_count'] }}</span>
                </div>
                <div class="bx-stats-mini-item bx-stats-mini-blue">
                    <span class="bx-stats-mini-label">Active POs</span>
                    <span class="bx-stats-mini-value">{{ $stats['active_pos'] }}</span>
                </div>
                <div class="bx-stats-mini-item bx-stats-mini-green">
                    <span class="bx-stats-mini-label">Pending GRN</span>
                    <span class="bx-stats-mini-value">{{ $stats['pending_grn'] }}</span>
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

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs bx-tabs-procurement">
            @foreach(['pending' => '① Pending PRs', 'active_pos' => '② Active POs', 'pending_grn' => '③ Pending GRN', 'history' => '④ History'] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')"
                        class="bx-tab {{ $activeTab === $tab ? 'active' : '' }}">
                    {{ $label }}
                    @if($tab === 'pending' && $stats['pending_count'] > 0)
                        <span class="bx-tab-badge bx-tab-badge-amber">{{ $stats['pending_count'] }}</span>
                    @elseif($tab === 'pending_grn' && $stats['pending_grn'] > 0)
                        <span class="bx-tab-badge bx-tab-badge-green">{{ $stats['pending_grn'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- ─── TAB 1: PENDING PRs ─── -->
    @if($activeTab === 'pending')
        <div class="bx-card bx-card-amber">
            <div class="bx-card-header bx-card-header-amber">
                <h3>Purchase Requests Awaiting Procurement Approval</h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Qty Required</th>
                                <th>Raised By</th>
                                <th>Linked Plan</th>
                                <th>Date Raised</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPRs as $pr)
                                <tr>
                                    <td>
                                        <div class="bx-material-cell">
                                            <span class="bx-material-name">{{ $pr->rawMaterial->name }}</span>
                                            <span class="bx-material-unit">Unit: {{ $pr->rawMaterial->unit }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="bx-quantity-amber">{{ number_format($pr->quantity, 2) }}</span>
                                        <span class="bx-quantity-unit">{{ $pr->rawMaterial->unit }}</span>
                                    </td>
                                    <td>{{ $pr->requestedBy->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($pr->production_request_id)
                                            <span class="bx-code">Plan #{{ $pr->production_request_id }}</span>
                                        @else
                                            <span class="text-gray">Manual</span>
                                        @endif
                                    </td>
                                    <td class="text-gray text-sm">{{ $pr->created_at->format('d M Y') }}</td>
                                    <td class="text-right">
                                        <button wire:click="openApproveModal({{ $pr->id }})"
                                                class="bx-btn bx-btn-warning bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Approve PR
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3>No pending purchase requests</h3>
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

    <!-- ─── TAB 2: ACTIVE POs ─── -->
    @if($activeTab === 'active_pos')
        <div class="bx-card bx-card-blue">
            <div class="bx-card-header bx-card-header-blue">
                <h3>Approved PRs — Issue PO & Track Delivery</h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Qty</th>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Exp. Delivery</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activePos as $pr)
                                <tr>
                                    <td>
                                        <span class="bx-material-name">{{ $pr->rawMaterial->name }}</span>
                                    </td>
                                    <td>
                                        <span class="bx-quantity">{{ number_format($pr->quantity, 2) }}</span>
                                        <span class="bx-quantity-unit">{{ $pr->rawMaterial->unit }}</span>
                                    </td>
                                    <td>
                                        @if($pr->po_number)
                                            <span class="bx-code bx-code-primary">{{ $pr->po_number }}</span>
                                        @else
                                            <span class="text-gray">Not issued</span>
                                        @endif
                                    </td>
                                    <td>{{ $pr->supplier->name ?? '—' }}</td>
                                    <td>
                                        @if($pr->unit_price)
                                            <span class="font-bold">{{ number_format($pr->unit_price, 2) }}</span>
                                        @else
                                            <span class="text-gray">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pr->unit_price)
                                            <span class="bx-quantity-success">{{ number_format($pr->total_amount, 2) }}</span>
                                        @else
                                            <span class="text-gray">—</span>
                                        @endif
                                    </td>
                                    <td class="text-gray text-sm">{{ $pr->expected_delivery_date ? $pr->expected_delivery_date->format('d M Y') : '—' }}</td>
                                    <td>
                                        @if($pr->status === 'approved')
                                            <span class="bx-badge bx-badge-warning">Approved</span>
                                        @elseif($pr->status === 'po_issued')
                                            <span class="bx-badge bx-badge-info">PO Issued</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="bx-actions">
                                            @if($pr->status === 'approved')
                                                <button wire:click="openPoModal({{ $pr->id }})"
                                                        class="bx-btn bx-btn-primary bx-btn-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    Issue PO
                                                </button>
                                            @elseif($pr->status === 'po_issued')
                                                <button wire:click="openRfqModal({{ $pr->id }})"
                                                        class="bx-btn bx-btn-secondary bx-btn-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"/>
                                                    </svg>
                                                    Preview PO
                                                </button>
                                                <button wire:click="openDeliverModal({{ $pr->id }})"
                                                        class="bx-btn bx-btn-success bx-btn-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Mark Delivered
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No active purchase orders</h3>
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

    <!-- ─── TAB 3: PENDING GRN ─── -->
    @if($activeTab === 'pending_grn')
        <div class="bx-card bx-card-green">
            <div class="bx-card-header bx-card-header-green">
                <h3>Delivered POs — Awaiting Warehouse GRN Confirmation</h3>
                <p class="bx-card-subtitle">Warehouse must confirm receipt via Stock-In before stock is updated.</p>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Material</th>
                                <th>Qty</th>
                                <th>Supplier</th>
                                <th>Total Value</th>
                                <th>Delivered At</th>
                                <th class="text-right">GRN Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingGrns as $pr)
                                <tr>
                                    <td>
                                        <span class="bx-code bx-code-primary">{{ $pr->po_number }}</span>
                                    </td>
                                    <td>
                                        <div class="bx-material-cell">
                                            <span class="bx-material-name">{{ $pr->rawMaterial->name }}</span>
                                            <span class="bx-material-unit">{{ $pr->rawMaterial->unit }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="bx-quantity-success">{{ number_format($pr->quantity, 2) }}</span>
                                    </td>
                                    <td>{{ $pr->supplier->name ?? '—' }}</td>
                                    <td class="font-bold text-success">${{ number_format($pr->total_amount, 2) }}</td>
                                    <td class="text-gray text-sm">{{ $pr->delivered_at ? $pr->delivered_at->format('d M Y H:i') : '—' }}</td>
                                    <td class="text-right">
                                        <div class="bx-actions">
                                            <span class="bx-badge bx-badge-warning bx-badge-pulse">⏳ Awaiting Warehouse GRN</span>
                                            <a href="{{ route('warehouse.stock-in') }}"
                                               class="bx-btn bx-btn-secondary bx-btn-xs">
                                                Go to Stock-In →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No pending GRN confirmations</h3>
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

    <!-- ─── TAB 4: HISTORY ─── -->
    @if($activeTab === 'history')
        <div class="bx-card bx-card-gray">
            <div class="bx-card-header">
                <h3>Completed Purchase History</h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Material</th>
                                <th>Qty</th>
                                <th>Supplier</th>
                                <th>Total Value</th>
                                <th>Total Paid</th>
                                <th>Balance Due</th>
                                <th class="text-right">Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $pr)
                                <tr>
                                    <td>
                                        <span class="bx-code bx-code-primary">{{ $pr->po_number }}</span>
                                    </td>
                                    <td class="font-bold">{{ $pr->rawMaterial->name }}</td>
                                    <td>{{ number_format($pr->quantity, 2) }}</td>
                                    <td>{{ $pr->supplier->name ?? '—' }}</td>
                                    <td class="font-bold">${{ number_format($pr->total_amount, 2) }}</td>
                                    <td class="font-bold text-success">${{ number_format($pr->total_paid, 2) }}</td>
                                    <td>
                                        @if($pr->balance_due > 0)
                                            <span class="bx-badge bx-badge-danger">${{ number_format($pr->balance_due, 2) }}</span>
                                        @else
                                            <span class="bx-badge bx-badge-success">Paid</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($pr->balance_due > 0)
                                            <button wire:click="openPaymentModal({{ $pr->id }})"
                                                    class="bx-btn bx-btn-accent bx-btn-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                                </svg>
                                                Record Payment
                                            </button>
                                        @else
                                            <span class="text-gray text-sm">Fully Paid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M3 3l9 9 9-9"/>
                                                </svg>
                                            </div>
                                            <h3>No completed purchases</h3>
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

    <!-- ─── APPROVE PR MODAL ─── -->
    @if($showApproveModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showApproveModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3>Approve Purchase Request?</h3>
                </div>
                <div class="bx-modal-body text-center">
                    <p class="text-gray-600">This will move the PR to the procurement queue for PO issuance. You can then select a supplier and issue a Purchase Order.</p>
                </div>
                <div class="bx-modal-footer justify-center">
                    <button wire:click="$set('showApproveModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button wire:click="approvePR" class="bx-btn bx-btn-warning">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approve
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── ISSUE PO MODAL ─── -->
    @if($showPoModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showPoModal', false)">
            <div class="bx-modal bx-modal-po">
                <form wire:submit.prevent="issuePO">
                    <div class="bx-modal-header">
                        <h3>Issue Purchase Order</h3>
                        <button type="button" wire:click="$set('showPoModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Supplier</label>
                                    <select wire:model="po_supplier_id" class="bx-select @error('po_supplier_id') bx-input-error @enderror">
                                        <option value="">— Select Supplier —</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->code }})</option>
                                        @endforeach
                                    </select>
                                    @error('po_supplier_id')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">PO Number</label>
                                <input type="text" wire:model="po_number" class="bx-input @error('po_number') bx-input-error @enderror" />
                                @error('po_number')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">Unit Price</label>
                                <input type="number" step="0.0001" wire:model="po_unit_price" class="bx-input @error('po_unit_price') bx-input-error @enderror" placeholder="0.00" />
                                @error('po_unit_price')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Expected Delivery Date</label>
                                    <input type="date" wire:model="po_expected_date" class="bx-input @error('po_expected_date') bx-input-error @enderror" />
                                    @error('po_expected_date')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showPoModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Issue PO →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELIVER MODAL ─── -->
    @if($showDeliverModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showDeliverModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3>Confirm Delivery</h3>
                </div>
                <div class="bx-modal-body text-center">
                    <p class="text-gray-600">Confirm that the supplier has delivered this order to the factory gate. <strong>Warehouse will then confirm GRN and update stock.</strong></p>
                </div>
                <div class="bx-modal-footer justify-center">
                    <button wire:click="$set('showDeliverModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button wire:click="markDelivered" class="bx-btn bx-btn-success">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Confirm Delivered
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── PAYMENT MODAL ─── -->
    @if($showPaymentModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showPaymentModal', false)">
            <div class="bx-modal bx-modal-payment">
                <form wire:submit.prevent="recordPayment">
                    <div class="bx-modal-header">
                        <h3>Record Purchase Payment</h3>
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Amount</label>
                                    <input type="number" step="0.01" wire:model="pay_amount" class="bx-input @error('pay_amount') bx-input-error @enderror" placeholder="0.00" />
                                    @error('pay_amount')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">Payment Method</label>
                                <select wire:model="pay_method" class="bx-select @error('pay_method') bx-input-error @enderror">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Reference / Slip No.</label>
                                <input type="text" wire:model="pay_reference" class="bx-input" placeholder="Bank slip or cheque number" />
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Payment Date</label>
                                    <input type="date" wire:model="pay_date" class="bx-input @error('pay_date') bx-input-error @enderror" />
                                    @error('pay_date')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Notes</label>
                                    <textarea wire:model="pay_notes" rows="2" class="bx-input" placeholder="Optional notes"></textarea>
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Receipt / Proof of Payment</label>
                                    <div class="bx-file-upload">
                                        <label class="bx-file-upload-label">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>
                                                @if($pay_receipt)
                                                    <span class="text-success">Photo Selected ✓</span>
                                                @else
                                                    <span>Click to upload Receipt</span>
                                                @endif
                                            </span>
                                            <span class="text-gray text-xs">PNG, JPG or PDF (MAX. 2MB)</span>
                                        </label>
                                        <input type="file" wire:model="pay_receipt" class="bx-file-input" accept="image/*" />
                                    </div>
                                    @error('pay_receipt')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror

                                    <div wire:loading wire:target="pay_receipt" class="bx-loading-text">Uploading digital receipt...</div>

                                    @if($pay_receipt)
                                        <div class="bx-receipt-preview">
                                            <img src="{{ $pay_receipt->temporaryUrl() }}" alt="Receipt" />
                                            <button type="button" wire:click="$set('pay_receipt', null)" class="bx-receipt-remove">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-accent">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── RFQ MODAL ─── -->
    @if($showRfqModal)
        <div class="bx-modal-overlay bx-modal-overlay-lg" wire:click.self="$set('showRfqModal', false)">
            <div class="bx-modal bx-modal-rfq">
                <div class="bx-modal-header bx-modal-header-rfq">
                    <div class="bx-modal-header-left">
                        <div class="bx-modal-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"/>
                            </svg>
                        </div>
                        <span>Purchase Order Preview</span>
                    </div>
                    <button type="button" wire:click="$set('showRfqModal', false)" class="bx-modal-close bx-modal-close-white">✕</button>
                </div>
                <div class="bx-modal-body bx-modal-body-rfq">
                    @if($viewingRfqId)
                        <iframe src="{{ route('finance.procurement.rfq', $viewingRfqId) }}" class="bx-rfq-iframe"></iframe>
                    @endif
                </div>
                <div class="bx-modal-footer-rfq">
                    <button wire:click="$set('showRfqModal', false)" class="bx-btn bx-btn-secondary bx-btn-sm">Close Preview</button>
                    <a href="{{ $viewingRfqId ? route('finance.procurement.rfq', $viewingRfqId) : '#' }}" target="_blank" class="bx-btn bx-btn-primary bx-btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Open Full Page
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
