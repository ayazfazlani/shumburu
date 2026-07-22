<!-- resources/views/livewire/warehouse/stock-out.blade.php -->
<div class="bx-page bx-page-stock-out">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Material Stock Out
            </h1>
            <p class="bx-header-subtitle">Issue raw materials to production line</p>
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
            <div class="bx-stat-label">Total Issues</div>
            <div class="bx-stat-value">{{ $stockOuts->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">On Process</div>
            <div class="bx-stat-value text-warning">{{ $stockOuts->where('status', 'material_on_process')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Completed</div>
            <div class="bx-stat-value text-success">{{ $stockOuts->where('status', 'completed')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Scrapped</div>
            <div class="bx-stat-value text-danger">{{ $stockOuts->where('status', 'scrapped')->count() }}</div>
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

    <!-- ─── FORM & INFO PANEL ─── -->
    <div class="bx-grid-2-1">
        <!-- Form -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @if($is_editing) Edit Stock Out Record @else New Stock Out Record @endif
                </h3>
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form">
                    <!-- Raw Material -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Raw Material</label>
                        <select wire:model="raw_material_id" class="bx-select @error('raw_material_id') bx-input-error @enderror">
                            <option value="">Select Raw Material</option>
                            @foreach ($rawMaterials as $material)
                                <option value="{{ $material->id }}">
                                    {{ $material->name }} ({{ $material->code }}) - {{ number_format($material->quantity, 3) }} {{ $material->unit }}
                                </option>
                            @endforeach
                        </select>
                        @error('raw_material_id')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Quantity (kg)</label>
                        <div class="bx-input-with-unit">
                            <input type="number" wire:model="quantity" step="0.001" min="0.001"
                                   class="bx-input @error('quantity') bx-input-error @enderror"
                                   placeholder="Enter quantity in kg" />
                            <span class="bx-input-unit">kg</span>
                        </div>
                        @error('quantity')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Batch Number -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Batch Number</label>
                        <input type="text" wire:model="batch_number"
                               class="bx-input @error('batch_number') bx-input-error @enderror"
                               placeholder="Enter batch number" />
                        @error('batch_number')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Issued Date -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Issued Date</label>
                        <input type="date" wire:model="issued_date"
                               class="bx-input @error('issued_date') bx-input-error @enderror" />
                        @error('issued_date')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="bx-input @error('notes') bx-input-error @enderror"
                                  placeholder="Additional notes (optional)"></textarea>
                        @error('notes')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="bx-form-group bx-form-full">
                        @if($is_editing)
                            <div class="flex gap-3">
                                <button type="submit" class="bx-btn bx-btn-primary flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update Record
                                </button>
                                <button type="button" wire:click="cancelEdit" class="bx-btn bx-btn-danger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </button>
                            </div>
                        @else
                            <button type="submit" class="bx-btn bx-btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Issue Material
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Stock Out Guidelines
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-info-list">
                    <div class="bx-info-item bx-info-warning">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4>Material Status</h4>
                            <p>Issued materials are tagged as "Material on Process"</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-blue">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Production Line</h4>
                            <p>Materials are issued to production line for HDPE pipe manufacturing</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-success">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Tracking</h4>
                            <p>All issued materials are tracked until completion</p>
                        </div>
                    </div>
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Material Status Flow</h4>
                <div class="bx-status-flow">
                    <div class="bx-status-flow-item">
                        <span class="bx-badge bx-badge-warning">On Process</span>
                        <span class="bx-status-flow-label">Material issued to production</span>
                    </div>
                    <div class="bx-status-flow-item">
                        <span class="bx-badge bx-badge-success">Completed</span>
                        <span class="bx-status-flow-label">Production finished successfully</span>
                    </div>
                    <div class="bx-status-flow-item">
                        <span class="bx-badge bx-badge-danger">Scrapped</span>
                        <span class="bx-status-flow-label">Material became waste</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── STOCK OUT HISTORY ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Stock Out History</h2>
            <span class="bx-badge bx-badge-secondary">{{ $stockOuts->total() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Raw Material</th>
                            <th>Quantity</th>
                            <th>Batch No</th>
                            <th>Issued Date</th>
                            <th>Issued By</th>
                            <th>Status</th>
                            <th class="hidden md:table-cell">Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockOuts as $item)
                            <tr>
                                <td class="text-gray-400 font-mono text-sm">{{ $item->id }}</td>
                                <td>{{ $item->rawMaterial->name ?? 'N/A' }}</td>
                                <td class="font-mono">{{ number_format($item->quantity, 3) }} kg</td>
                                <td><span class="bx-code">{{ $item->batch_number }}</span></td>
                                <td>{{ $item->issued_date->format('d-m-Y') }}</td>
                                <td>{{ $item->issuedBy->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $statusLabels = [
                                            'material_on_process' => ['label' => 'On Process', 'class' => 'bx-badge-warning'],
                                            'completed' => ['label' => 'Completed', 'class' => 'bx-badge-success'],
                                            'scrapped' => ['label' => 'Scrapped', 'class' => 'bx-badge-danger']
                                        ];
                                        $status = $statusLabels[$item->status] ?? ['label' => ucfirst(str_replace('_', ' ', $item->status)), 'class' => 'bx-badge-gray'];
                                    @endphp
                                    <div class="bx-status-dropdown">
                                        <span class="bx-badge {{ $status['class'] }} cursor-pointer" onclick="this.nextElementSibling.classList.toggle('open')">
                                            {{ $status['label'] }}
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                        <div class="bx-dropdown-menu">
                                            <a wire:click="updateStatus({{ $item->id }}, 'material_on_process')" class="bx-dropdown-item">On Process</a>
                                            <a wire:click="updateStatus({{ $item->id }}, 'completed')" class="bx-dropdown-item">Completed</a>
                                            <a wire:click="updateStatus({{ $item->id }}, 'scrapped')" class="bx-dropdown-item">Scrapped</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell">{{ $item->notes ?? '-' }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $item->id }})"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="setDeleteId({{ $item->id }})"
                                                class="bx-action bx-action-delete"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── PAGINATION ─── -->
        @if($stockOuts->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $stockOuts->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $stockOuts->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $stockOuts->total() }}</strong> records
                </div>
                <div class="bx-pagination">
                    {{ $stockOuts->links('components.pagination') }}
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
                    <p class="bx-delete-text">This action will restore {{ number_format($deleteQuantity, 3) }} kg back to stock. This cannot be undone.</p>
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
