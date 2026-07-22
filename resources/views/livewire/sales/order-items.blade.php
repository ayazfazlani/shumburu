<!-- resources/views/livewire/sales/order-items.blade.php -->
<div class="bx-page bx-page-order-items">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Order Items - {{ $productionOrder->order_number ?? 'N/A' }}
            </h1>
            <p class="bx-header-subtitle">
                @if($productionOrder)
                    Customer: {{ $productionOrder->customer->name ?? 'N/A' }}
                @endif
            </p>
        </div>
        <div class="bx-header-right">
            <button wire:click="create" class="bx-btn bx-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Item
            </button>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Items</div>
            <div class="bx-stat-value">{{ $orderItems->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Quantity</div>
            <div class="bx-stat-value text-blue">{{ number_format($orderItems->sum('quantity'), 2) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Order Total</div>
            <div class="bx-stat-value text-success">${{ number_format($orderItems->sum('total_price'), 2) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Fully Reserved</div>
            <div class="bx-stat-value text-green">{{ $orderItems->where('reserved_quantity', '>=', 'quantity')->count() }}</div>
        </div>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session()->has('message'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
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

    <!-- ─── SEARCH ─── -->
    <div class="bx-search-bar">
        <div class="bx-search">
            <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Search by product name or code..." class="bx-search-input" />
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            ID
                            @if($sortField === 'id')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th>Product</th>
                        <th class="hidden md:table-cell">Specs</th>
                        <th wire:click="sortBy('quantity')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Quantity
                            @if($sortField === 'quantity')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="hidden sm:table-cell">Unit</th>
                        <th wire:click="sortBy('unit_price')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Unit Price
                            @if($sortField === 'unit_price')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('total_price')" class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                            Total Price
                            @if($sortField === 'total_price')
                                <span class="bx-sort-icon">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th>Stock Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $item)
                        <tr>
                            <td class="text-gray-400 font-mono text-sm">{{ $item->id }}</td>
                            <td>
                                <div class="bx-product-cell">
                                    <span class="bx-product-name">{{ $item->product->name ?? 'N/A' }}</span>
                                    <span class="bx-product-code">{{ $item->product->code ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                @if($item->od || $item->pn || $item->sdr)
                                    <div class="flex flex-wrap gap-1">
                                        @if($item->od) <span class="bx-code">{{ $item->od }}</span> @endif
                                        @if($item->pn) <span class="bx-code bx-code-info">{{ $item->pn }}</span> @endif
                                        @if($item->sdr) <span class="bx-code bx-code-warning">SDR{{ $item->sdr }}</span> @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">No specs</span>
                                @endif
                            </td>
                            <td class="font-bold">{{ $item->formatted_quantity }}</td>
                            <td class="hidden sm:table-cell">{{ $item->unit }}</td>
                            <td>${{ $item->formatted_unit_price }}</td>
                            <td class="font-bold text-success">${{ $item->formatted_total_price }}</td>
                            <td>
                                <div class="bx-stock-status">
                                    @if($item->reserved_quantity >= $item->quantity)
                                        <span class="bx-badge bx-badge-success">Fully Reserved</span>
                                    @elseif($item->reserved_quantity > 0)
                                        <span class="bx-badge bx-badge-warning">Partial: {{ number_format($item->reserved_quantity, 2) }}</span>
                                    @else
                                        <span class="bx-badge bx-badge-danger">No Stock Reserved</span>
                                    @endif

                                    @php
                                        $demand = \App\Models\StockDemand::where('order_item_id', $item->id)->latest()->first();
                                    @endphp

                                    @if($demand)
                                        @if($demand->status === 'pending')
                                            <span class="bx-status-text bx-status-pending">⏳ Awaiting Warehouse Authorization</span>
                                        @elseif($demand->status === 'raised')
                                            <span class="bx-status-text bx-status-raised">🏭 Production Requested by Warehouse</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="edit({{ $item->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                            class="bx-action bx-action-delete"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @if($item->reserved_quantity < $item->quantity && (!$demand || $demand->status === 'fulfilled'))
                                        <button wire:click="raiseStockRequest({{ $item->id }})"
                                                class="bx-btn bx-btn-primary bx-btn-xs">
                                            🚀 Request Stock
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <h3>No order items found</h3>
                                    <p>Add items to this order by clicking the "Add Item" button.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($orderItems->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $orderItems->firstItem() ?? 0 }}</strong>
                to <strong>{{ $orderItems->lastItem() ?? 0 }}</strong>
                of <strong>{{ $orderItems->total() }}</strong> items
            </div>
            <div class="bx-pagination">
                {{ $orderItems->links() }}
            </div>
        </div>
    @endif

    <!-- ─── TOTAL SUMMARY ─── -->
    @if($orderItems->count() > 0)
        <div class="bx-summary">
            <div class="bx-summary-item">
                <span class="bx-summary-label">Total Items:</span>
                <span class="bx-summary-value">{{ $orderItems->total() }}</span>
            </div>
            <div class="bx-summary-item">
                <span class="bx-summary-label">Order Total:</span>
                <span class="bx-summary-value bx-summary-total">${{ number_format($orderItems->sum('total_price'), 2) }}</span>
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay" wire:click.self="closeModal">
            <div class="bx-modal">
                <form wire:submit.prevent="save">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $isEditing ? 'Edit Order Item' : 'Add Order Item' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Product Selection -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Product</label>
                                <select wire:model.live="productId" class="bx-select @error('productId') bx-input-error @enderror">
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($productId)
                                    <div class="bx-helper-text {{ $availableStock > 0 ? 'bx-helper-text-green' : 'bx-helper-text-red' }}">
                                        Available Stock to Promise: {{ number_format($availableStock, 2) }}
                                    </div>
                                @endif
                                @error('productId')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Quantity & Unit -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Quantity</label>
                                <input wire:model.live="quantity" type="number" step="0.01" min="0.01"
                                       class="bx-input @error('quantity') bx-input-error @enderror"
                                       placeholder="Enter quantity" />
                                @error('quantity')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">Unit</label>
                                <select wire:model.defer="unit" class="bx-select @error('unit') bx-input-error @enderror">
                                    <option value="meter">Meter</option>
                                    <option value="roll">Roll</option>
                                    <option value="piece">Piece</option>
                                    <option value="kg">Kilogram</option>
                                </select>
                                @error('unit')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Technical Specifications -->
                            <div class="bx-form-full">
                                <div class="bx-specs-wrapper">
                                    <div class="bx-specs-title">Technical Specifications</div>
                                    <div class="bx-specs-grid">
                                        <div class="bx-form-group">
                                            <label class="bx-form-label">OD (Diameter)</label>
                                            <input wire:model="od" type="text"
                                                   class="bx-input" placeholder="e.g. 20mm" />
                                        </div>
                                        <div class="bx-form-group">
                                            <label class="bx-form-label">PN (Pressure)</label>
                                            <input wire:model="pn" type="text"
                                                   class="bx-input" placeholder="e.g. PN16" />
                                        </div>
                                        <div class="bx-form-group">
                                            <label class="bx-form-label">SDR</label>
                                            <input wire:model="sdr" type="text"
                                                   class="bx-input" placeholder="e.g. 11" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Unit Price -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Unit Price</label>
                                <input wire:model.live="unitPrice" type="number" step="0.01" min="0.01"
                                       class="bx-input @error('unitPrice') bx-input-error @enderror"
                                       placeholder="Enter unit price" />
                                @error('unitPrice')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Total Price -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Total Price</label>
                                <input type="text" value="{{ $totalPrice }}" readonly
                                       class="bx-input bx-input-readonly" />
                            </div>

                            <!-- Auto Reserve -->
                            <div class="bx-form-full">
                                <label class="bx-checkbox">
                                    <input type="checkbox" wire:model="autoReserve" />
                                    <span>Automatically reserve available stock for this item</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closeModal" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEditing ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $isEditing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELETE MODAL ─── -->
    @if($showDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showDeleteModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Order Item
                    </h3>
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-modal-close">
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the order item.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteOrderItem" class="bx-btn bx-btn-danger">
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
