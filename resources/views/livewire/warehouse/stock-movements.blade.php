<!-- resources/views/livewire/warehouse/stock-movement.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Material Stock Movement
            </h1>
            <p class="bx-header-subtitle">Record incoming and outgoing raw materials from a single screen</p>
        </div>
        <div class="bx-header-right">
            <a href="{{ route('warehouse.index') }}" class="bx-btn bx-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Warehouse
            </a>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Stock In Records</div>
            <div class="bx-stat-value text-success">{{ $stats['total_in'] }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Stock Out Records</div>
            <div class="bx-stat-value text-danger">{{ $stats['total_out'] }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Quantity In</div>
            <div class="bx-stat-value text-success">{{ number_format($stats['qty_in'], 2) }} kg</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Quantity Out</div>
            <div class="bx-stat-value text-danger">{{ number_format($stats['qty_out'], 2) }} kg</div>
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

    <!-- ─── MAIN CONTENT ─── -->
    <div class="bx-grid-2-1">
        <!-- ─── FORM ─── -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    @if($is_editing)
                        Edit Stock {{ strtoupper($movement_type) }} Record
                    @else
                        New Stock {{ strtoupper($movement_type) }} Record
                    @endif
                </h3>
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form">

                    {{-- ─── TYPE TOGGLE ─── --}}
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Movement Type</label>
                        <div class="bx-type-toggle">
                            <button type="button"
                                    wire:click="$set('movement_type','in')"
                                    @disabled($is_editing && $edit_type === 'out')
                                    class="bx-type-btn bx-type-in {{ $movement_type === 'in' ? 'active' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Stock In
                            </button>
                            <button type="button"
                                    wire:click="$set('movement_type','out')"
                                    @disabled($is_editing && $edit_type === 'in')
                                    class="bx-type-btn bx-type-out {{ $movement_type === 'out' ? 'active' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Stock Out
                            </button>
                        </div>
                        @if($is_editing)
                            <span class="bx-error text-amber">
                                Editing existing record — type cannot be changed.
                            </span>
                        @endif
                    </div>

                    <!-- Raw Material -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Raw Material</label>
                        <select wire:model="raw_material_id" class="bx-select w-full @error('raw_material_id') bx-input-error @enderror">
                            <option value="">Select Raw Material</option>
                            @foreach ($rawMaterials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                            @endforeach
                        </select>
                        @error('raw_material_id') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Quantity -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Quantity (kg)</label>
                        <div class="bx-input-with-unit">
                            <input type="number" wire:model="quantity" step="0.001" min="0.001"
                                   class="bx-input w-full @error('quantity') bx-input-error @enderror"
                                   placeholder="Enter quantity in kg">
                            <span class="bx-input-unit">kg</span>
                        </div>
                        @error('quantity') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Item Code -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Item Code</label>
                        <input type="text" wire:model="batch_number"
                               class="bx-input w-full @error('batch_number') bx-input-error @enderror"
                               placeholder="Enter item code">
                        @error('batch_number') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date (label changes) -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">
                            {{ $movement_type === 'in' ? 'Received Date' : 'Issued Date' }}
                        </label>
                        <input type="date" wire:model="movement_date"
                               class="bx-input w-full @error('movement_date') bx-input-error @enderror">
                        @error('movement_date') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="bx-input w-full @error('notes') bx-input-error @enderror"
                                  placeholder="Additional notes (optional)"></textarea>
                        @error('notes') <span class="bx-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="bx-form-group bx-form-full">
                        @if($is_editing)
                            <div class="flex gap-3">
                                <button type="submit" class="bx-btn bx-btn-primary flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update {{ strtoupper($movement_type) }} Record
                                </button>
                                <button type="button" wire:click="cancelEdit" class="bx-btn bx-btn-danger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </button>
                            </div>
                        @else
                            <button type="submit"
                                    class="bx-btn {{ $movement_type === 'in' ? 'bx-btn-success' : 'bx-btn-danger' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Record Stock {{ strtoupper($movement_type) }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- ─── INFO PANEL ─── -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $movement_type === 'in' ? 'Stock In' : 'Stock Out' }} Guidelines
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-info-list">
                    @if($movement_type === 'in')
                        <div class="bx-info-item bx-info-green">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4>Incoming Materials</h4>
                                <p>Record all raw materials received into the warehouse with proper batch tracking.</p>
                            </div>
                        </div>
                        <div class="bx-info-item bx-info-blue">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4>Stock Increases</h4>
                                <p>Saving a Stock In record automatically increases the raw material's available quantity.</p>
                            </div>
                        </div>
                    @else
                        <div class="bx-info-item bx-info-amber">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h4>Stock Validation</h4>
                                <p>You cannot issue more than what is currently available in the warehouse.</p>
                            </div>
                        </div>
                        <div class="bx-info-item bx-info-blue">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4>Process Tracking</h4>
                                <p>Issued materials are marked "material on process" and can be updated later.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Available Raw Materials</h4>
                <div class="bx-material-list">
                    @foreach ($rawMaterials as $material)
                        <div class="bx-material-item">
                            <div>
                                <div class="bx-material-name">{{ $material->name }}</div>
                                <div class="bx-material-code">{{ $material->code }}</div>
                            </div>
                            <div class="bx-material-stats">
                                <span class="bx-code">{{ $material->unit }}</span>
                                <span class="bx-material-stock {{ $material->quantity > 0 ? 'bx-stock-positive' : 'bx-stock-negative' }}">
                                    {{ number_format($material->quantity, 1) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ─── MOVEMENT RECORDS ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>
                Stock Movement Records
                <span class="bx-badge bx-badge-secondary">{{ $movements->total() }} Records</span>
            </h2>
            <div class="bx-filter-group">
                <label class="bx-form-label" style="margin:0">Filter:</label>
                <select wire:model.live="filter_type" class="bx-select">
                    <option value="all">All Movements</option>
                    <option value="in">Stock In Only</option>
                    <option value="out">Stock Out Only</option>
                </select>
            </div>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Raw Material</th>
                            <th>Quantity</th>
                            <th>Item Code</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mv)
                            <tr>
                                <td>
                                    @if($mv->type === 'in')
                                        <span class="bx-badge bx-badge-success">IN</span>
                                    @else
                                        <span class="bx-badge bx-badge-danger">OUT</span>
                                    @endif
                                </td>
                                <td>{{ $mv->raw_material->name ?? 'N/A' }}</td>
                                <td class="font-mono font-bold">{{ number_format($mv->quantity, 2) }} kg</td>
                                <td><span class="bx-code">{{ $mv->batch_number }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($mv->date)->format('d-m-Y') }}</td>
                                <td>{{ $mv->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if($mv->type === 'out')
                                        <select wire:change="updateStatus({{ $mv->id }}, $event.target.value)"
                                                class="bx-select"
                                                style="padding:4px 8px;font-size:12px;">
                                            @foreach(['material_on_process','consumed','returned','cancelled'] as $st)
                                                <option value="{{ $st }}" @selected($mv->status === $st)>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="bx-badge bx-badge-secondary">received</span>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell">{{ $mv->notes ?? '—' }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $mv->id }}, '{{ $mv->type }}')"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="setDeleteId({{ $mv->id }}, '{{ $mv->type }}')"
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
                                <td colspan="9" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                        </div>
                                        <h3>No stock movement records found</h3>
                                        <p>Start recording stock in or stock out to see records here.</p>
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

    <!-- ─── DELETE MODAL ─── -->
    @if($showDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="closeDeleteModal">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Confirm Delete
                    </h3>
                    <button type="button" wire:click="closeDeleteModal" class="bx-modal-close">
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
                    <p class="bx-delete-text">
                        @if($delete_type === 'in')
                            This will deduct <strong>{{ number_format($deleteQuantity, 2) }}</strong> kg from
                            <strong>{{ $deleteMaterialName }}</strong> stock.
                        @else
                            This will return <strong>{{ number_format($deleteQuantity, 2) }}</strong> kg back to
                            <strong>{{ $deleteMaterialName }}</strong> stock.
                        @endif
                    </p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="closeDeleteModal" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="delete" class="bx-btn bx-btn-danger">
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

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('scroll-to-form', () => {
            document.querySelector('form')?.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
