<!-- resources/views/livewire/warehouse/scrap-wastes.blade.php -->
<div class="bx-page bx-page-scrap-waste">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Scrap/Waste Records
        </h1>
        <p class="bx-header-subtitle">Track and manage material waste from production processes</p>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Records</div>
            <div class="bx-stat-value">{{ $scrapWastes->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">This Month</div>
            <div class="bx-stat-value text-blue">{{ $scrapWastes->where('waste_date', '>=', now()->startOfMonth())->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today</div>
            <div class="bx-stat-value text-warning">{{ $scrapWastes->where('waste_date', today())->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Waste (kg)</div>
            <div class="bx-stat-value text-danger">
                @php
                    $totalWaste = $scrapWastes->sum(function($sw) {
                        return $sw->materialStockOutLine->quantity_consumed ?? 0;
                    });
                @endphp
                {{ number_format($totalWaste, 2) }}
            </div>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ $isEdit ? 'Edit Scrap/Waste Record' : 'New Scrap/Waste Record' }}
            </h3>
        </div>
        <div class="bx-card-body">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="bx-form">
                <!-- Material Stock Out Line -->
                <div class="bx-form-group">
                    <label class="bx-form-label required">Material Stock Out Line</label>
                    <select wire:model="material_stock_out_line_id" class="bx-select @error('material_stock_out_line_id') bx-input-error @enderror">
                        <option value="">Select Stock Out Line</option>
                        @foreach($stockOutLines as $line)
                            <option value="{{ $line->id }}">
                                #{{ $line->id }} - {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}
                                | Batch: {{ $line->materialStockOut->batch_number ?? '-' }}
                                | Qty: {{ number_format($line->quantity_consumed, 2) }} kg
                                | Available: {{ number_format($line->available_quantity ?? 0, 2) }} kg
                            </option>
                        @endforeach
                    </select>
                    @error('material_stock_out_line_id')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Reason -->
                <div class="bx-form-group">
                    <label class="bx-form-label required">Reason</label>
                    <input type="text" wire:model="reason"
                           class="bx-input @error('reason') bx-input-error @enderror"
                           placeholder="e.g. Production defect, Machine malfunction" />
                    @error('reason')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Waste Date -->
                <div class="bx-form-group">
                    <label class="bx-form-label required">Waste Date</label>
                    <input type="date" wire:model="waste_date"
                           class="bx-input @error('waste_date') bx-input-error @enderror" />
                    @error('waste_date')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Recorded By -->
                <div class="bx-form-group">
                    <label class="bx-form-label required">Recorded By</label>
                    <select wire:model="recorded_by" class="bx-select @error('recorded_by') bx-input-error @enderror">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('recorded_by')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="bx-form-group bx-form-full">
                    <label class="bx-form-label">Notes</label>
                    <textarea wire:model="notes" rows="3"
                              class="bx-input @error('notes') bx-input-error @enderror"
                              placeholder="Additional notes about the waste..."></textarea>
                    @error('notes')
                        <span class="bx-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="bx-form-group bx-form-full">
                    @if($isEdit)
                        <div class="flex gap-3">
                            <button type="submit" class="bx-btn bx-btn-warning flex-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Update Record
                            </button>
                            <button type="button" wire:click="$set('isEdit', false)" class="bx-btn bx-btn-secondary">
                                Cancel
                            </button>
                        </div>
                    @else
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Record Waste
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ─── RECORDS TABLE ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Scrap/Waste Records</h2>
            <span class="bx-badge bx-badge-secondary">{{ $scrapWastes->total() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stock Out Line</th>
                            <th>Reason</th>
                            <th class="hidden sm:table-cell">Waste Date</th>
                            <th class="hidden md:table-cell">Recorded By</th>
                            <th class="hidden lg:table-cell">Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scrapWastes as $sw)
                            <tr>
                                <td class="text-gray font-mono text-sm">{{ $sw->id }}</td>
                                <td>
                                    <div class="bx-line-cell">
                                        <span class="bx-line-material">#{{ $sw->material_stock_out_line_id }}</span>
                                        @if($sw->materialStockOutLine)
                                            <div class="bx-line-meta">
                                                <span class="bx-code">{{ $sw->materialStockOutLine->materialStockOut->rawMaterial->name ?? 'N/A' }}</span>
                                                <span class="bx-code">Batch: {{ $sw->materialStockOutLine->materialStockOut->batch_number ?? '-' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="bx-badge bx-badge-warning">{{ $sw->reason ?? '-' }}</span>
                                </td>
                                <td class="hidden sm:table-cell">{{ $sw->waste_date ? $sw->waste_date->format('Y-m-d') : '-' }}</td>
                                <td class="hidden md:table-cell">{{ $sw->recordedBy->name ?? 'N/A' }}</td>
                                <td class="hidden lg:table-cell">{{ Str::limit($sw->notes ?? '-', 30) }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $sw->id }})"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $sw->id }})"
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
                                <td colspan="7" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                        <h3>No scrap/waste records found</h3>
                                        <p>Start tracking material waste from production processes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── PAGINATION ─── -->
        @if($scrapWastes->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $scrapWastes->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $scrapWastes->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $scrapWastes->total() }}</strong> entries
                </div>
                <div class="bx-pagination">
                    {{ $scrapWastes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
