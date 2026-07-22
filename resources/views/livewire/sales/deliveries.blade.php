<!-- resources/views/livewire/sales/deliveries.blade.php -->
<div class="bx-page bx-page-deliveries">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Delivered Orders
            </h1>
            <p class="bx-header-subtitle">View and manage all delivered production orders</p>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Orders</div>
            <div class="bx-stat-value">{{ $orders->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Delivered</div>
            <div class="bx-stat-value text-success">{{ $orders->where('status', 'delivered')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Completed</div>
            <div class="bx-stat-value text-blue">{{ $orders->where('status', 'completed')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">In Production</div>
            <div class="bx-stat-value text-warning">{{ $orders->where('status', 'in_production')->count() }}</div>
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
                <input type="text"
                       wire:model.live.debounce.300ms="orderSearch"
                       placeholder="Search orders..."
                       class="bx-search-input" />
            </div>
            <select wire:model.live="orderPerPage" class="bx-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="bx-toolbar-right">
            <!-- Optional: Add export or filter buttons -->
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th class="hidden md:table-cell">Notes</th>
                        <th class="text-center">Details</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <span class="bx-order-number">#{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <span class="bx-code">{{ $order->customer->name ?? '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bx-badge-warning', 'label' => 'Pending'],
                                        'approved' => ['class' => 'bx-badge-info', 'label' => 'Approved'],
                                        'in_production' => ['class' => 'bx-badge-primary', 'label' => 'In Production'],
                                        'completed' => ['class' => 'bx-badge-success', 'label' => 'Completed'],
                                        'delivered' => ['class' => 'bx-badge-secondary', 'label' => 'Delivered'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['class' => 'bx-badge-gray', 'label' => ucfirst($order->status)];
                                @endphp
                                <span class="bx-badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                            </td>
                            <td>{{ $order->requested_date ? $order->requested_date->format('Y-m-d') : '-' }}</td>
                            <td class="hidden md:table-cell">{{ Str::limit($order->notes ?? '-', 30) }}</td>
                            <td class="text-center">
                                <a href="{{ route('order-items', $order->id) }}"
                                   class="bx-action bx-action-view"
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openOrderEditModal({{ $order->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmOrderDelete({{ $order->id }})"
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
                            <td colspan="7" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3>No delivered orders found</h3>
                                    <p>Orders will appear here once they are marked as delivered.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
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

    <!-- ─── EDIT MODAL ─── -->
    @if($showOrderModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showOrderModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="saveOrder">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ $isOrderEdit ? 'Edit Order' : 'Create Order' }}
                        </h3>
                        <button type="button" wire:click="$set('showOrderModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Order Number -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Order Number</label>
                                <input type="text" wire:model.defer="order_number"
                                       class="bx-input @error('order_number') bx-input-error @enderror"
                                       placeholder="Order Number" readonly />
                                @error('order_number')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Customer -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Customer</label>
                                <select wire:model.defer="customer_id" class="bx-select @error('customer_id') bx-input-error @enderror">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Status</label>
                                <select wire:model.defer="status" class="bx-select @error('status') bx-input-error @enderror">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="in_production">In Production</option>
                                    <option value="completed">Completed</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                                @error('status')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Requested Date -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Requested Date</label>
                                <input type="date" wire:model.defer="requested_date"
                                       class="bx-input @error('requested_date') bx-input-error @enderror" />
                                @error('requested_date')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Notes</label>
                                <textarea wire:model.defer="notes" rows="3"
                                          class="bx-input @error('notes') bx-input-error @enderror"
                                          placeholder="Additional notes"></textarea>
                                @error('notes')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showOrderModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isOrderEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $isOrderEdit ? 'Update Order' : 'Create Order' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELETE MODAL ─── -->
    @if($showOrderDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showOrderDeleteModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Order
                    </h3>
                    <button type="button" wire:click="$set('showOrderDeleteModal', false)" class="bx-modal-close">
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the order and all associated data.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showOrderDeleteModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteOrder" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
