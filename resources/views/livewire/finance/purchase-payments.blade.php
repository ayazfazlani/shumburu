<!-- resources/views/livewire/finance/purchase-payments.blade.php -->
<div class="bx-page bx-page-purchase-payments">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Purchase Payments
            </h1>
            <p class="bx-header-subtitle">Accounts Payable — Track supplier payments against Purchase Orders</p>
        </div>
        <div class="bx-header-right">
            <div class="bx-stats-mini">
                <div class="bx-stats-mini-item bx-stats-mini-green">
                    <span class="bx-stats-mini-label">Total Paid</span>
                    <span class="bx-stats-mini-value">{{ number_format($stats['total_paid'], 0) }}</span>
                </div>
                <div class="bx-stats-mini-item bx-stats-mini-red">
                    <span class="bx-stats-mini-label">Outstanding AP</span>
                    <span class="bx-stats-mini-value">{{ number_format($stats['total_outstanding'], 0) }}</span>
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

    <!-- ─── FILTERS ─── -->
    <div class="bx-filters-bar">
        <div class="bx-filters-left">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search by material or PO #..." class="bx-search-input" />
            </div>
            <select wire:model="filterSupplier" class="bx-select">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- ─── RECEIVED POs TABLE ─── -->
    <div class="bx-card">
        <div class="bx-card-header">
            <h3>Received POs — Payment Ledger</h3>
            <span class="bx-badge bx-badge-secondary">{{ $receivedPOs->total() }} POs</span>
        </div>
        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Material</th>
                            <th>Supplier</th>
                            <th class="text-right">Total Value</th>
                            <th class="text-right">Total Paid</th>
                            <th class="text-right">Balance Due</th>
                            <th class="text-center">Payments</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivedPOs as $pr)
                            <tr>
                                <td>
                                    <span class="bx-code bx-code-primary">#{{ $pr->po_number }}</span>
                                </td>
                                <td>
                                    <div class="bx-material-cell">
                                        <span class="bx-material-name">{{ $pr->rawMaterial->name }}</span>
                                        <span class="bx-material-qty">{{ number_format($pr->quantity, 2) }} {{ $pr->rawMaterial->unit }}</span>
                                    </div>
                                </td>
                                <td class="font-medium text-gray-600">{{ $pr->supplier->name ?? '—' }}</td>
                                <td class="text-right font-bold">{{ number_format($pr->total_amount, 2) }}</td>
                                <td class="text-right font-bold text-success">{{ number_format($pr->total_paid, 2) }}</td>
                                <td class="text-right">
                                    @if($pr->balance_due > 0)
                                        <span class="bx-badge bx-badge-danger">{{ number_format($pr->balance_due, 2) }}</span>
                                    @else
                                        <span class="bx-badge bx-badge-success">Fully Paid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="bx-code">{{ $pr->purchasePayments->count() }} Payments</span>
                                </td>
                                <td class="text-right">
                                    @if($pr->balance_due > 0)
                                        <button wire:click="openPaymentModal({{ $pr->id }})"
                                                class="bx-btn bx-btn-accent bx-btn-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Record Payment
                                        </button>
                                    @else
                                        <span class="bx-badge bx-badge-gray">Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                        </div>
                                        <h3>No received POs found</h3>
                                        <p>Start recording supplier payments.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($receivedPOs->hasPages())
            <div class="bx-pagination-wrap">
                {{ $receivedPOs->links() }}
            </div>
        @endif
    </div>

    <!-- ─── RECENT PAYMENTS LOG ─── -->
    <div class="bx-card bx-card-recent">
        <div class="bx-card-header">
            <h3>Recent Payment Transactions</h3>
            <span class="bx-badge bx-badge-secondary">{{ $payments->count() }} Transactions</span>
        </div>
        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th class="text-right">Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <span class="bx-code">{{ $payment->purchaseRequest->po_number ?? '—' }}</span>
                                </td>
                                <td class="font-medium text-gray-600">{{ $payment->supplier->name ?? '—' }}</td>
                                <td class="text-right font-bold text-success">{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="bx-badge bx-badge-info">{{ str_replace('_', ' ', $payment->payment_method ?? 'N/A') }}</span>
                                </td>
                                <td class="font-mono text-xs text-gray-400">{{ $payment->reference_number ?? '—' }}</td>
                                <td class="text-xs font-medium text-gray-400">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="text-xs font-medium text-gray-500">{{ $payment->recordedBy->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                        </div>
                                        <h3>No payments recorded yet</h3>
                                        <p>Start recording supplier payments.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ─── PAYMENT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="recordPayment">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            Record Purchase Payment
                        </h3>
                        <button type="button" wire:click="$set('showModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Amount -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Amount</label>
                                <input type="number" step="0.01" wire:model="pay_amount"
                                       class="bx-input bx-input-lg @error('pay_amount') bx-input-error @enderror"
                                       placeholder="Enter amount" />
                                @error('pay_amount')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Payment Method</label>
                                <select wire:model="pay_method" class="bx-select @error('pay_method') bx-input-error @enderror">
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online Payment</option>
                                </select>
                                @error('pay_method')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Reference -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Reference / Slip No.</label>
                                <input type="text" wire:model="pay_reference"
                                       class="bx-input @error('pay_reference') bx-input-error @enderror"
                                       placeholder="Bank slip, cheque number..." />
                                @error('pay_reference')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Payment Date -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Payment Date</label>
                                <input type="date" wire:model="pay_date"
                                       class="bx-input @error('pay_date') bx-input-error @enderror" />
                                @error('pay_date')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Notes</label>
                                <textarea wire:model="pay_notes" rows="3"
                                          class="bx-input @error('pay_notes') bx-input-error @enderror"
                                          placeholder="Payment notes"></textarea>
                                @error('pay_notes')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
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
</div>
