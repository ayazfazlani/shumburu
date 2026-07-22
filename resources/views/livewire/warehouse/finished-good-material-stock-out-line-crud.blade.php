<!-- resources/views/livewire/warehouse/finished-good-material.blade.php -->
<div class="bx-page bx-page-fg-material-links">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Finished Good Material Stock Out Links
        </h1>
        <p class="bx-header-subtitle">Link finished goods to raw material consumption batches</p>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Links</div>
            <div class="bx-stat-value">{{ $records->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Finished Goods</div>
            <div class="bx-stat-value text-blue">{{ $records->groupBy('finished_good_id')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Materials Used</div>
            <div class="bx-stat-value text-warning">{{ $records->groupBy('material_stock_out_line_id')->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Quantity</div>
            <div class="bx-stat-value text-green">{{ number_format($records->sum('quantity_used'), 2) }} kg</div>
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

    <!-- ─── FORM ─── -->
    <div class="bx-card">
        <div class="bx-card-header">
            <h3>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ $isEdit ? 'Edit Link' : 'Create New Link' }}
            </h3>
        </div>
        <div class="bx-card-body">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="bx-form">
                <!-- Finished Good -->
                <div class="bx-form-group bx-form-full">
                    <label class="bx-form-label required">Finished Good</label>
                    <select wire:model="finished_good_id" class="bx-select @error('finished_good_id') bx-input-error @enderror">
                        <option value="">Select Finished Good</option>
                        @foreach($finishedGoods as $fg)
                            <option value="{{ $fg->id }}">
                                {{ $fg->product->name ?? 'N/A' }} | Type: {{ $fg->type ?? '-' }} | Batch: {{ $fg->batch_number ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('finished_good_id')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Raw Materials Used -->
                <div class="bx-form-full">
                    <div class="bx-materials-section">
                        <div class="bx-materials-header">
                            <h4>Raw Materials Used</h4>
                            <button type="button" wire:click="addUsageRow" class="bx-btn bx-btn-secondary bx-btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Material
                            </button>
                        </div>

                        @foreach($usages as $index => $usage)
                            <div class="bx-material-row" wire:key="usage-row-{{ $index }}">
                                <div class="bx-material-row-fields">
                                    <div class="bx-form-group">
                                        <select wire:model="usages.{{ $index }}.material_stock_out_line_id"
                                                wire:change="$refresh"
                                                class="bx-select @error('usages.'.$index.'.material_stock_out_line_id') bx-input-error @enderror">
                                            <option value="">Select Material</option>
                                            @foreach($stockOutLines as $line)
                                                @php
                                                    $available = $line->available_quantity ?? 0;
                                                @endphp
                                                <option value="{{ $line->id }}">
                                                    {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}
                                                    | Batch: {{ $line->materialStockOut->batch_number ?? '-' }}
                                                    | Line: {{ $line->productionLine->name ?? '-' }}
                                                    | Consumed: {{ number_format($line->quantity_consumed, 2) }}
                                                    | Available: {{ number_format($available, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(isset($usages[$index]['material_stock_out_line_id']) && $usages[$index]['material_stock_out_line_id'])
                                            @php
                                                $selectedLine = $stockOutLines->firstWhere('id', $usages[$index]['material_stock_out_line_id']);
                                                $available = $selectedLine ? $selectedLine->available_quantity : 0;
                                            @endphp
                                            <span class="bx-helper-text bx-helper-text-blue">
                                                Available: {{ number_format($available, 2) }} kg
                                            </span>
                                        @endif
                                        @error('usages.'.$index.'.material_stock_out_line_id')
                                            <span class="bx-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="bx-form-group">
                                        <input type="number"
                                               wire:model="usages.{{ $index }}.quantity_used"
                                               wire:change="$refresh"
                                               class="bx-input @error('usages.' . $index . '.quantity_used') bx-input-error @enderror"
                                               step="0.01" min="0"
                                               placeholder="Quantity Used (kg)" />
                                        @error('usages.' . $index . '.quantity_used')
                                            <span class="bx-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <button type="button" wire:click="removeUsageRow({{ $index }})"
                                        class="bx-btn bx-btn-danger bx-btn-sm bx-material-remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                <div class="bx-form-full">
                    <div class="flex gap-3">
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            {{ $isEdit ? 'Update Link' : 'Create Link' }}
                        </button>
                        @if($isEdit)
                            <button type="button" wire:click="$set('isEdit', false)" class="bx-btn bx-btn-secondary">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── RECORDS TABLE ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Recorded Links</h2>
            <span class="bx-badge bx-badge-secondary">{{ $records->total() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Finished Good</th>
                            <th>Material Stock Out Line</th>
                            <th class="text-center">Quantity Used</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $link)
                            <tr>
                                <td>
                                    @php $fg = $link->finishedGood; @endphp
                                    @if($fg)
                                        <div class="bx-fg-cell">
                                            <span class="bx-fg-name">{{ $fg->product->name ?? 'N/A' }}</span>
                                            <div class="bx-fg-meta">
                                                <span class="bx-code">{{ $fg->type ?? '-' }}</span>
                                                <span class="bx-code">Batch: {{ $fg->batch_number ?? '-' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray">#{{ $link->finished_good_id }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php $line = $link->materialStockOutLine; @endphp
                                    @if($line)
                                        <div class="bx-line-cell">
                                            <span class="bx-line-material">{{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</span>
                                            <div class="bx-line-meta">
                                                <span class="bx-code">Batch: {{ $line->materialStockOut->batch_number ?? '-' }}</span>
                                                <span class="bx-code">Line: {{ $line->productionLine->name ?? '-' }}</span>
                                            </div>
                                            <div class="bx-line-available">
                                                Available:
                                                <span class="{{ ($line->available_quantity ?? 0) > 0 ? 'text-blue-600' : 'text-red-600' }} font-semibold">
                                                    {{ number_format($line->available_quantity ?? 0, 2) }} kg
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray">#{{ $link->material_stock_out_line_id }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="bx-quantity-used">{{ number_format($link->quantity_used, 2) }}</span>
                                    <span class="bx-unit">kg</span>
                                </td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $link->id }})"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $link->id }})"
                                                class="bx-action bx-action-delete"
                                                onclick="return confirm('Are you sure?')"
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
                                <td colspan="4" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <h3>No links found</h3>
                                        <p>Create a link to associate finished goods with raw material consumption.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── PAGINATION ─── -->
        @if($records->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $records->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $records->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $records->total() }}</strong> entries
                </div>
                <div class="bx-pagination">
                    {{ $records->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
