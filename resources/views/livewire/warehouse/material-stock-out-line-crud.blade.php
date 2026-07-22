<!-- resources/views/livewire/warehouse/material-stock-out-lines.blade.php -->
<div class="bx-page bx-page-stock-out-lines">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Material Stock Out Lines
        </h1>
        <p class="bx-header-subtitle">Batch entry and consumption tracking for production materials</p>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Lines</div>
            <div class="bx-stat-value">{{ $materialStockOutLines->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active Batches</div>
            <div class="bx-stat-value text-blue">{{ $materialStockOutLines->where('quantity_returned', null)->where('available_quantity', '>', 0)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Used</div>
            <div class="bx-stat-value text-warning">{{ number_format($materialStockOutLines->sum('total_used_quantity'), 2) }} kg</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Returned</div>
            <div class="bx-stat-value text-green">{{ number_format($materialStockOutLines->sum('quantity_returned'), 2) }} kg</div>
        </div>
    </div>

    <!-- ─── BATCH CREATE FORM ─── -->
    <div class="bx-card">
        <div class="bx-card-header">
            <h3>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Batch Entry
            </h3>
        </div>
        <div class="bx-card-body">
            <form wire:submit.prevent="saveBatch" class="bx-form">
                <!-- Production Line & Shift -->
                <div class="bx-form-group">
                    <label class="bx-form-label required">Production Line</label>
                    <select wire:model="production_line_id" class="bx-select">
                        <option value="">Select Production Line</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->name ?? ('Line #' . $line->id) }}</option>
                        @endforeach
                    </select>
                    @error('production_line_id')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bx-form-group">
                    <label class="bx-form-label required">Shift</label>
                    <select wire:model="shift" class="bx-select">
                        <option value="">Select Shift</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                    </select>
                    @error('shift')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Materials Section -->
                <div class="bx-form-full">
                    <div class="bx-materials-section">
                        <div class="bx-materials-header">
                            <h4>Materials</h4>
                            <button type="button" wire:click="addRow" class="bx-btn bx-btn-secondary bx-btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Material
                            </button>
                        </div>

                        @foreach($materials as $index => $material)
                            <div class="bx-material-row" wire:key="material-row-{{ $index }}">
                                <div class="bx-material-row-fields">
                                    <div class="bx-form-group">
                                        <select wire:model="materials.{{ $index }}.material_stock_out_id"
                                                wire:change="$refresh"
                                                class="bx-select">
                                            <option value="">Select Material</option>
                                            @foreach($stockOuts as $stockOut)
                                                @php
                                                    $available = $this->getAvailableQuantity($stockOut->id);
                                                @endphp
                                                <option value="{{ $stockOut->id }}">
                                                    {{ $stockOut->rawMaterial->name ?? 'N/A' }}
                                                    | Batch: {{ $stockOut->batch_number ?? '-' }}
                                                    | Stocked: {{ round($stockOut->quantity, 2) }}
                                                    | Available: {{ round($available, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(isset($materials[$index]['material_stock_out_id']) && $materials[$index]['material_stock_out_id'])
                                            @php
                                                $available = $this->getAvailableQuantity($materials[$index]['material_stock_out_id']);
                                            @endphp
                                            <span class="bx-helper-text bx-helper-text-blue">
                                                Available: {{ round($available, 2) }} kg
                                            </span>
                                        @endif
                                        @error('materials.' . $index . '.material_stock_out_id')
                                            <span class="bx-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="bx-form-group">
                                        <input type="number"
                                               wire:model="materials.{{ $index }}.quantity_consumed"
                                               wire:change="$refresh"
                                               placeholder="Quantity (kg)"
                                               step="0.01" min="0.01"
                                               class="bx-input @error('materials.' . $index . '.quantity_consumed') bx-input-error @enderror" />
                                        @error('materials.' . $index . '.quantity_consumed')
                                            <span class="bx-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if($index > 0)
                                    <button type="button" wire:click="removeRow({{ $index }})"
                                            class="bx-btn bx-btn-danger bx-btn-sm bx-material-remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                <div class="bx-form-full">
                    <button type="submit" class="bx-btn bx-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Batch
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── RECORDS TABLE ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Recorded Stock Out Lines</h2>
            <span class="bx-badge bx-badge-secondary">{{ $materialStockOutLines->count() }} Records</span>
        </div>

        @if(session('message'))
            <div class="bx-alert bx-alert-success">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('message') }}
            </div>
        @endif

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Raw Material</th>
                            <th>Batch</th>
                            <th>Production Line</th>
                            <th class="text-center">Qty Consumed</th>
                            <th class="text-center">Used</th>
                            <th class="text-center">Returned</th>
                            <th class="text-center">Available</th>
                            <th>Shift</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materialStockOutLines as $line)
                            @php
                                $used = $line->total_used_quantity ?? 0;
                                $returned = $line->quantity_returned ?? 0;
                                $available = $line->available_quantity ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="bx-material-cell">
                                        <span class="bx-material-name">{{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</span>
                                        <span class="bx-material-code">{{ $line->materialStockOut->rawMaterial->code ?? '' }}</span>
                                    </div>
                                </td>
                                <td><span class="bx-code">{{ $line->materialStockOut->batch_number ?? '-' }}</span></td>
                                <td>{{ $line->productionLine->name ?? '-' }}</td>
                                <td class="text-center font-mono font-bold">{{ number_format($line->quantity_consumed, 2) }}</td>
                                <td class="text-center">
                                    <span class="bx-badge bx-badge-warning">{{ number_format($used, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($returned > 0)
                                        <div>
                                            <span class="bx-badge bx-badge-success">{{ number_format($returned, 2) }}</span>
                                            @if($line->returned_at)
                                                <div class="bx-helper-text">Returned: {{ \Carbon\Carbon::parse($line->returned_at)->format('Y-m-d') }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="bx-stock-badge {{ $available > 0 ? 'bx-stock-ok' : 'bx-stock-low' }}">
                                        {{ number_format($available, 2) }}
                                    </span>
                                </td>
                                <td><span class="bx-code">{{ $line->shift }}</span></td>
                                <td>
                                    <div class="bx-actions">
                                        @if($available > 0)
                                            <button wire:click="openReturnModal({{ $line->id }})"
                                                    class="bx-action bx-action-edit"
                                                    title="Return stock">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        @endif
                                        <button wire:click="delete({{ $line->id }})"
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ─── RETURN MODAL ─── -->
    @if($showReturnModal)
        <div class="bx-modal-overlay" wire:click.self="closeReturnModal">
            <div class="bx-modal bx-modal-return">
                <form wire:submit.prevent="processReturn">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Return Stock
                        </h3>
                        <button type="button" wire:click="closeReturnModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        @if($returnLineId)
                            @php
                                $line = \App\Models\MaterialStockOutLine::find($returnLineId);
                                $available = $line ? $line->available_quantity : 0;
                            @endphp
                            <div class="bx-return-info">
                                <div class="bx-return-info-item">
                                    <span class="bx-return-info-label">Material</span>
                                    <span class="bx-return-info-value">{{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</span>
                                </div>
                                <div class="bx-return-info-item">
                                    <span class="bx-return-info-label">Batch</span>
                                    <span class="bx-return-info-value">{{ $line->materialStockOut->batch_number ?? '-' }}</span>
                                </div>
                                <div class="bx-return-info-item bx-return-info-highlight">
                                    <span class="bx-return-info-label">Available to Return</span>
                                    <span class="bx-return-info-value bx-return-info-value-blue">{{ number_format($available, 2) }} kg</span>
                                </div>
                            </div>
                        @endif

                        <div class="bx-form-group">
                            <label class="bx-form-label required">Return Quantity (kg)</label>
                            <input type="number"
                                   wire:model="returnQuantity"
                                   step="0.01" min="0.01"
                                   max="{{ $available ?? 0 }}"
                                   class="bx-input bx-input-lg @error('returnQuantity') bx-input-error @enderror"
                                   placeholder="Enter quantity to return" />
                            @error('returnQuantity')
                                <span class="bx-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="bx-form-group">
                            <label class="bx-form-label">Return Notes</label>
                            <textarea wire:model="returnNotes"
                                      class="bx-input bx-textarea"
                                      rows="3"
                                      placeholder="Optional notes about the return"></textarea>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closeReturnModal" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Process Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
