<!-- resources/views/livewire/sales/payments.blade.php -->
<div class="bx-page bx-page-payments">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Payments
            </h1>
            <p class="bx-header-subtitle">Record, edit, and delete payments. Upload slip images or PDFs.</p>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Payments</div>
            <div class="bx-stat-value">{{ $payments->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Amount</div>
            <div class="bx-stat-value text-success">${{ number_format($payments->sum('amount'), 2) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">This Month</div>
            <div class="bx-stat-value text-blue">{{ $payments->where('payment_date', '>=', now()->startOfMonth())->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today</div>
            <div class="bx-stat-value text-warning">{{ $payments->where('payment_date', today())->count() }}</div>
        </div>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session('message'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bx-alert bx-alert-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- ─── TOOLBAR ─── -->
    <div class="bx-toolbar">
        <div class="bx-toolbar-left">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" wire:model.debounce.300ms="paymentSearch" placeholder="Search slip ref..." class="bx-search-input" />
            </div>
            <select wire:model="paymentPerPage" class="bx-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="bx-toolbar-right">
            <button wire:click="openPaymentCreateModal" class="bx-btn bx-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">New Payment</span>
                <span class="sm:hidden">Add</span>
            </button>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Orders</th>
                        <th>Customer</th>
                        <th class="text-right">Amount</th>
                        <th>Payment Method</th>
                        <th>Slip Ref</th>
                        <th>Slip File</th>
                        <th>Proforma Invoice</th>
                        <th>Payment Date</th>
                        <th class="hidden md:table-cell">Notes</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <span class="bx-code">{{ $payment->productionOrder->order_number ?? '-' }}</span>
                            </td>
                            <td>{{ $payment->customer->name ?? '-' }}</td>
                            <td class="text-right font-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->payment_method)
                                    <span class="bx-badge bx-badge-info">{{ $payment->payment_method }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="font-mono text-xs">{{ Str::limit($payment->bank_slip_reference ?? '-', 20) }}</td>
                            <td>
                                @if ($payment->bank_slip_reference && Storage::disk('public')->exists($payment->bank_slip_reference))
                                    @php
                                        $fileUrl = asset('storage/' . $payment->bank_slip_reference);
                                        $isImage = in_array(strtolower(pathinfo($payment->bank_slip_reference, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    @if($isImage)
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $fileUrl }}" alt="Slip" class="bx-slip-thumbnail"
                                                 wire:click="viewFile('{{ $payment->bank_slip_reference }}')" />
                                            <button wire:click="viewFile('{{ $payment->bank_slip_reference }}')" class="bx-link">View</button>
                                        </div>
                                    @else
                                        <button wire:click="viewFile('{{ $payment->bank_slip_reference }}')" class="bx-link">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            View PDF
                                        </button>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>{{ $payment->proforma_invoice_number ?? '-' }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '-' }}</td>
                            <td class="hidden md:table-cell">{{ Str::limit($payment->notes ?? '-', 30) }}</td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openPaymentEditModal({{ $payment->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmPaymentDelete({{ $payment->id }})"
                                            class="bx-action bx-action-delete"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                    <h3>No payments found</h3>
                                    <p>Start recording customer payments.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($payments->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong>
                to <strong>{{ $payments->lastItem() ?? 0 }}</strong>
                of <strong>{{ $payments->total() }}</strong> payments
            </div>
            <div class="bx-pagination">
                {{ $payments->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showPaymentModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showPaymentModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="savePayment" enctype="multipart/form-data">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            {{ $isPaymentEdit ? 'Edit Payment' : 'Record Payment' }}
                        </h3>
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        @if($errors->any())
                            <div class="bx-alert bx-alert-danger">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4>Please fix the following errors:</h4>
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="bx-form">
                            <!-- Production Order -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Production Order</label>
                                <select wire:model="order_id" class="bx-select @error('order_id') bx-input-error @enderror">
                                    <option value="">Select Production Order</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}">
                                            Order #{{ $order->order_number }} - {{ $order->customer->display_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_id')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Customer -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Customer</label>
                                <select wire:model="customer_id" class="bx-select @error('customer_id') bx-input-error @enderror">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Amount</label>
                                <input type="number" wire:model="amount" class="bx-input @error('amount') bx-input-error @enderror"
                                       min="0" step="0.01" placeholder="0.00" />
                                @error('amount')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="bx-form-group">
                                <label class="bx-form-label">Payment Method</label>
                                <input type="text" wire:model="payment_method" class="bx-input @error('payment_method') bx-input-error @enderror"
                                       placeholder="e.g., Bank Transfer, Cash" />
                                @error('payment_method')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Bank Slip Reference -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Bank Slip Reference</label>
                                <input type="text" wire:model="bank_slip_reference" class="bx-input @error('bank_slip_reference') bx-input-error @enderror"
                                       placeholder="Bank Slip Reference" />
                                @error('bank_slip_reference')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Slip File -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Slip File (Image/PDF)</label>
                                <input type="file" wire:model="slip_file" class="bx-file-input" accept=".jpg,.jpeg,.png,.pdf" />
                                @error('slip_file')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror

                                <!-- Image Preview -->
                                @if($slip_file)
                                    <div class="bx-file-preview">
                                        <div class="bx-file-preview-header">
                                            <span class="bx-file-preview-label">Preview:</span>
                                            <button type="button" wire:click="removeSlipFile" class="bx-btn bx-btn-danger bx-btn-sm">Remove</button>
                                        </div>
                                        @php
                                            $isImage = in_array(strtolower($slip_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <div wire:loading.remove wire:target="slip_file">
                                                @php
                                                    try {
                                                        $previewUrl = $slip_file->isPreviewable() ? $slip_file->temporaryUrl() : null;
                                                    } catch (\Exception $e) {
                                                        $previewUrl = null;
                                                    }
                                                @endphp
                                                @if($previewUrl)
                                                    <div class="bx-file-preview-image">
                                                        <img src="{{ $previewUrl }}" alt="Preview"
                                                             onclick="window.open('{{ $previewUrl }}', '_blank')" />
                                                        <button onclick="window.open('{{ $previewUrl }}', '_blank')" class="bx-link">View Full Size</button>
                                                    </div>
                                                @else
                                                    <div class="bx-file-preview-file">
                                                        <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <div>
                                                            <p class="bx-file-name">{{ $slip_file->getClientOriginalName() }}</p>
                                                            <p class="bx-file-size">{{ number_format($slip_file->getSize() / 1024, 2) }} KB</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div wire:loading wire:target="slip_file" class="bx-file-loading">
                                                <span class="bx-spinner"></span>
                                                <span>Uploading...</span>
                                            </div>
                                        @else
                                            <div class="bx-file-preview-file">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                <div>
                                                    <p class="bx-file-name">{{ $slip_file->getClientOriginalName() }}</p>
                                                    <p class="bx-file-size">PDF File ({{ number_format($slip_file->getSize() / 1024, 2) }} KB)</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Existing File -->
                                @if($existing_slip_file && !$slip_file && Storage::disk('public')->exists($existing_slip_file))
                                    <div class="bx-file-preview bx-file-existing">
                                        <div class="bx-file-preview-header">
                                            <span class="bx-file-preview-label">Current File:</span>
                                            <button type="button" wire:click="removeSlipFile" class="bx-btn bx-btn-danger bx-btn-sm">Remove</button>
                                        </div>
                                        @php
                                            $fileUrl = asset('storage/' . $existing_slip_file);
                                            $isImage = in_array(strtolower(pathinfo($existing_slip_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <div class="bx-file-preview-image">
                                                <img src="{{ $fileUrl }}" alt="Current Slip"
                                                     wire:click="viewFile('{{ $existing_slip_file }}')" />
                                                <button wire:click="viewFile('{{ $existing_slip_file }}')" class="bx-link">View Full Size</button>
                                            </div>
                                        @else
                                            <div class="bx-file-preview-file">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                <div>
                                                    <p class="bx-file-name">{{ basename($existing_slip_file) }}</p>
                                                    <button wire:click="viewFile('{{ $existing_slip_file }}')" class="bx-link">View PDF</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Proforma Invoice -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Proforma Invoice Number</label>
                                <input type="text" wire:model="proforma_invoice_number" class="bx-input @error('proforma_invoice_number') bx-input-error @enderror"
                                       placeholder="Proforma Invoice Number" />
                                @error('proforma_invoice_number')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Payment Date -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Payment Date</label>
                                <input type="date" wire:model="payment_date" class="bx-input @error('payment_date') bx-input-error @enderror" />
                                @error('payment_date')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Notes</label>
                                <textarea wire:model="notes" rows="3" class="bx-input @error('notes') bx-input-error @enderror"
                                          placeholder="Additional notes"></textarea>
                                @error('notes')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="savePayment" class="bx-btn bx-btn-primary">
                            <span wire:loading.remove wire:target="savePayment">{{ $isPaymentEdit ? 'Update' : 'Record' }}</span>
                            <span wire:loading wire:target="savePayment" class="flex items-center gap-2">
                                <span class="bx-spinner bx-spinner-sm"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELETE MODAL ─── -->
    @if($showPaymentDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showPaymentDeleteModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Payment
                    </h3>
                    <button type="button" wire:click="$set('showPaymentDeleteModal', false)" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bx-modal-body text-center">
                    <div class="bx-delete-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h4 class="bx-delete-title">Are you sure?</h4>
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the payment and all associated data.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showPaymentDeleteModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deletePayment" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── FILE VIEWER MODAL ─── -->
    @if($showImageViewer)
        <div class="bx-modal-overlay bx-modal-overlay-dark" wire:click="closeImageViewer">
            <div class="bx-modal bx-modal-viewer" wire:click.stop>
                <div class="bx-modal-header">
                    <h3>{{ $viewerFileName }}</h3>
                    <div class="flex items-center gap-2">
                        @if($viewerFileUrl)
                            <a href="{{ $viewerFileUrl }}" download class="bx-btn bx-btn-primary bx-btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        @endif
                        <button wire:click="closeImageViewer" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="bx-modal-body bx-modal-body-viewer">
                    @if($viewerIsImage && $viewerFileUrl)
                        <div class="bx-viewer-image">
                            <img src="{{ $viewerFileUrl }}" alt="{{ $viewerFileName }}" />
                        </div>
                    @elseif($viewerFileUrl)
                        <div class="bx-viewer-pdf">
                            <svg class="bx-viewer-pdf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="bx-viewer-pdf-name">{{ $viewerFileName }}</p>
                            <p class="bx-viewer-pdf-hint">PDF files cannot be previewed inline</p>
                            <a href="{{ $viewerFileUrl }}" target="_blank" class="bx-btn bx-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Open PDF in New Tab
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
