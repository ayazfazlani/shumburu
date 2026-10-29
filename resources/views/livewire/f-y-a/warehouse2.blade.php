<div class="bx-page">
    {{-- ═════════════════════ HEADER ═════════════════════ --}}
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                FYA Warehouse Movements
            </h1>
            <p class="bx-header-subtitle">Record and track finished goods movements in FYA storage</p>
        </div>
        <div class="bx-header-right">
            <a href="{{ route('warehouse.index') }}" class="bx-btn bx-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Warehouse
            </a>
        </div>
    </div>

    {{-- ═════════════════════ STATS ═════════════════════ --}}
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Movements</div>
            <div class="bx-stat-value">{{ $movements->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Stock In (page)</div>
            <div class="bx-stat-value text-success">
                {{ $movements->where('movement_type', 'in')->count() }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Stock Out (page)</div>
            <div class="bx-stat-value text-danger">
                {{ $movements->where('movement_type', 'out')->count() }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active Batches</div>
            <div class="bx-stat-value text-warning">{{ $stockBalance->count() }}</div>
        </div>
    </div>

    {{-- ═════════════════════ ALERTS ═════════════════════ --}}
    @if (session('message'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bx-alert bx-alert-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ═════════════════════ FORM + LIVE STOCK ═════════════════════ --}}
    <div class="bx-grid-2-1" id="movement-form">

        {{-- ─── FORM ─── --}}
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    @if($is_editing) Edit Movement @else New Movement @endif
                </h3>
                @if($is_editing)
                    <button wire:click="cancelEdit" class="bx-btn bx-btn-sm bx-btn-secondary">
                        Cancel Edit
                    </button>
                @endif
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form">

                    {{-- Type toggle --}}
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Movement Type</label>
                        <div class="bx-type-toggle">
                            <button type="button" wire:click="$set('movement_type','in')"
                                @if($is_editing) disabled @endif
                                class="bx-type-btn bx-type-in {{ $movement_type === 'in' ? 'active' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                IN
                            </button>
                            <button type="button" wire:click="$set('movement_type','out')"
                                @if($is_editing) disabled @endif
                                class="bx-type-btn bx-type-out {{ $movement_type === 'out' ? 'active' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                OUT
                            </button>
                        </div>
                        @error('movement_type') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Finished good --}}
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Finished Good</label>
                        <select wire:model="finished_good_id"
                            @if($is_editing) disabled @endif
                            class="bx-select w-full @error('finished_good_id') bx-input-error @enderror">
                            <option value="">Select finished good</option>
                            @foreach ($finishedGoods as $fg)
                                <option value="{{ $fg->id }}">
                                    {{ $fg->product->name }} — Batch: {{ $fg->batch_number }} - Type: {{ $fg->type == 'cut' ? 'meter' : $fg->type }} - Length/m: {{ number_format($fg->length_m ?? 0, 2) }} - Surface: {{ $fg->surface }}
                                </option>
                            @endforeach
                        </select>
                        @error('finished_good_id') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Batch --}}
                    <div class="bx-form-group">
                        <label class="bx-form-label">Batch Number</label>
                        <input type="text" wire:model="batch_number"
                            @if($is_editing) readonly @endif
                            class="bx-input w-full @error('batch_number') bx-input-error @enderror"
                            placeholder="Batch Number">
                        @error('batch_number') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="bx-form-group">
                        <label class="bx-form-label">Quantity</label>
                        <div class="bx-input-with-unit">
                            <input type="number" step="0.001" min="0.001" wire:model="quantity"
                                class="bx-input w-full @error('quantity') bx-input-error @enderror"
                                placeholder="Quantity">
                            <span class="bx-input-unit">pcs</span>
                        </div>
                        @error('quantity') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Purpose --}}
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Purpose</label>
                        <select wire:model.live="purpose"
                            @if($is_editing) disabled @endif
                            class="bx-select w-full @error('purpose') bx-input-error @enderror">
                            <option value="for_stock">For Stock</option>
                            <option value="for_customer_order">For Customer Order</option>
                        </select>
                        @error('purpose') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Customer (conditional) --}}
                    @if ($purpose === 'for_customer_order')
                        <div class="bx-form-group">
                            <label class="bx-form-label required">Customer</label>
                            <select wire:model="customer_id"
                                @if($is_editing) disabled @endif
                                class="bx-select w-full @error('customer_id') bx-input-error @enderror">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        @can('can see customer name')
                                            {{ $customer->name }} —
                                        @endcan
                                        {{ $customer->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="bx-error">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Date --}}
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Movement Date</label>
                        <input type="date" wire:model="movement_date"
                            class="bx-input w-full @error('movement_date') bx-input-error @enderror">
                        @error('movement_date') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                            class="bx-input w-full @error('notes') bx-input-error @enderror"
                            placeholder="Additional notes (optional)"></textarea>
                        @error('notes') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="bx-form-group bx-form-full">
                        <div class="flex gap-3">
                            <button type="submit"
                                class="bx-btn bx-btn-primary flex-1 {{ $movement_type === 'out' ? 'bx-btn-danger' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                @if($is_editing) Update Movement @else Save Movement @endif
                            </button>
                            <button type="button" wire:click="$refresh" class="bx-btn bx-btn-secondary">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ─── LIVE STOCK CARD GRID ─── --}}
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Current FYA Stock
                </h3>
            </div>
            <div class="bx-card-body">

                {{-- Search --}}
                <div class="bx-materials-search">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="stock_search"
                        placeholder="Search product or batch...">
                </div>

                <div class="bx-materials-header">
                    <h4 class="bx-list-title">Available Batches</h4>
                    <span class="bx-badge bx-badge-secondary">{{ $filteredStock->count() }} batches</span>
                </div>

                <div class="bx-materials-grid">
                    @forelse ($filteredStock as $row)
                        @php
                            $balance  = (float) $row->balance;
                            $maxRef   = max($filteredStock->max('balance') ?? 0, 1);
                            $pct      = $maxRef > 0 ? min(100, ($balance / $maxRef) * 100) : 0;
                            $isEmpty  = $balance <= 0;
                            $isLow    = $balance > 0 && $balance < 50;
                            $barColor = $isEmpty ? 'empty' : ($isLow ? 'low' : 'ok');
                        @endphp

                        <div class="bx-material-card {{ $isEmpty ? 'bx-material-card-muted' : '' }}">
                            <div class="bx-material-card-head">
                                <div class="bx-material-card-name" title="{{ $row->finishedGood->product->name ?? '-' }}">
                                    {{ $row->finishedGood->product->name ?? '-' }}
                                </div>
                                @if($row->purpose === 'for_customer_order')
                                    <span class="bx-badge bx-badge-warning">ORDER</span>
                                @else
                                    <span class="bx-badge bx-badge-secondary">STOCK</span>
                                @endif
                            </div>

                            <div class="bx-material-card-sub">
                                <span class="bx-code">{{ $row->batch_number }}</span>
                                @if($row->customer_id && $row->customer)
                                    <span class="bx-material-card-cust">
                                        → {{ $row->customer->name ?? $row->customer->code }}
                                    </span>
                                @endif
                            </div>

                            <div class="bx-material-card-body">
                                <div class="bx-material-qty {{ $isEmpty ? 'text-danger' : ($isLow ? 'text-warning' : 'text-success') }}">
                                    <span class="bx-material-qty-value">{{ number_format($balance, 3) }}</span>
                                    <span class="bx-material-qty-unit">pcs</span>
                                </div>
                            </div>

                            <div class="bx-material-bar">
                                <div class="bx-material-bar-fill {{ $barColor }}" style="width: {{ $pct }}%"></div>
                            </div>

                            <div class="bx-material-card-status">
                                @if($isEmpty)
                                    <span class="bx-status-dot bx-status-empty"></span> Empty
                                @elseif($isLow)
                                    <span class="bx-status-dot bx-status-low"></span> Low
                                @else
                                    <span class="bx-status-dot bx-status-ok"></span> In stock
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bx-materials-empty">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>No stock available in FYA.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═════════════════════ MOVEMENTS TABLE ═════════════════════ --}}
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>
                Movements
                <span class="bx-badge bx-badge-secondary">{{ $movements->total() }} records</span>
            </h2>
            <button type="button" wire:click="$toggle('show_filters')" class="bx-btn bx-btn-secondary bx-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                {{ $show_filters ? 'Hide Filters' : 'Filters' }}
            </button>
        </div>

        {{-- Filter bar (collapsible) --}}
        @if($show_filters)
            <div class="bx-filter-bar">
                <div class="bx-filter-grid">
                    <div class="bx-filter-item">
                        <label class="bx-form-label">Type</label>
                        <select wire:model.live="filter_type" class="bx-select w-full">
                            <option value="">All Types</option>
                            <option value="in">IN</option>
                            <option value="out">OUT</option>
                        </select>
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">Product</label>
                        <select wire:model.live="filter_product_id" class="bx-select w-full">
                            <option value="">All Products</option>
                            @foreach ($finishedGoods as $fg)
                                <option value="{{ $fg->id }}">{{ $fg->product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">Batch</label>
                        <input type="text" wire:model.live.debounce.300ms="filter_batch"
                            class="bx-input w-full" placeholder="Batch">
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">Purpose</label>
                        <select wire:model.live="filter_purpose" class="bx-select w-full">
                            <option value="">All Purposes</option>
                            <option value="for_stock">For Stock</option>
                            <option value="for_customer_order">For Customer Order</option>
                        </select>
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">Customer</label>
                        <select wire:model.live="filter_customer_id" class="bx-select w-full">
                            <option value="">All Customers</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    @can('can see customer name'){{ $customer->name }} — @endcan{{ $customer->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">From</label>
                        <input type="date" wire:model.live="filter_date_from" class="bx-input w-full">
                    </div>
                    <div class="bx-filter-item">
                        <label class="bx-form-label">To</label>
                        <input type="date" wire:model.live="filter_date_to" class="bx-input w-full">
                    </div>
                    <div class="bx-filter-item bx-filter-actions">
                        <button type="button"
                            wire:click="$set('filter_type',''); $set('filter_product_id',''); $set('filter_batch',''); $set('filter_purpose',''); $set('filter_customer_id',''); $set('filter_date_from',''); $set('filter_date_to','');"
                            class="bx-btn bx-btn-secondary bx-btn-sm">
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Quantity</th>
                            <th>Purpose</th>
                            <th>Customer</th>
                            <th>Recorded By</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $move)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($move->movement_date)->format('d-m-Y') }}</td>
                                <td>
                                    @if($move->movement_type === 'in')
                                        <span class="bx-badge bx-badge-success">IN</span>
                                    @else
                                        <span class="bx-badge bx-badge-danger">OUT</span>
                                    @endif
                                </td>
                                <td>{{ $move->finishedGood->product->name ?? '-' }}</td>
                                <td><span class="bx-code">{{ $move->batch_number }}</span></td>
                                <td class="font-mono font-bold">{{ number_format($move->quantity, 3) }}</td>
                                <td>
                                    <span class="bx-badge bx-badge-secondary">
                                        {{ str_replace('_', ' ', $move->purpose) }}
                                    </span>
                                </td>
                                <td>
                                    @if($move->customer)
                                        {{ $move->customer->name ?? '-' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-xs">{{ $move->createdBy->name ?? '-' }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $move->id }})"
                                            class="bx-action bx-action-edit" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="setDeleteId({{ $move->id }})"
                                            class="bx-action bx-action-delete" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <h3>No movements found</h3>
                                        <p>Adjust your filters or record a new movement.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($movements->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $movements->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $movements->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $movements->total() }}</strong> entries
                </div>
                <div class="bx-pagination">
                    {{ $movements->links('components.pagination') }}
                </div>
            </div>
        @endif
    </div>

    {{-- ═════════════════════ DELETE MODAL ═════════════════════ --}}
    @if($showDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="closeDeleteModal">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Confirm Delete
                    </h3>
                    <button type="button" wire:click="closeDeleteModal" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="bx-modal-body text-center">
                    <div class="bx-delete-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="bx-delete-title">Are you sure?</h4>
                    <p class="bx-delete-text">
                        This will delete the movement for batch
                        <strong>{{ $deleteBatchNumber ?? '-' }}</strong>.
                        This action cannot be undone and may affect stock balance.
                    </p>
                </div>
                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="closeDeleteModal" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="delete" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('scroll-to-form', () => {
        document.getElementById('movement-form')?.scrollIntoView({ behavior: 'smooth' });
    });
</script>
@endscript
