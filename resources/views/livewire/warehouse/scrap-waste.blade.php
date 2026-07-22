<!-- resources/views/livewire/warehouse/scrap-waste.blade.php -->
<div class="bx-page bx-page-scrap-waste">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Scrap/Waste Record
            </h1>
            <p class="bx-header-subtitle">Record daily scrap and waste materials</p>
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
            <div class="bx-stat-label">Total Records</div>
            <div class="bx-stat-value">{{ $scrapWasteRecords->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Scrap (kg)</div>
            <div class="bx-stat-value text-warning">{{ number_format($scrapWasteRecords->sum('quantity'), 2) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today's Records</div>
            <div class="bx-stat-value text-blue">{{ $scrapWasteRecords->where('waste_date', today())->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Avg Waste Ratio</div>
            <div class="bx-stat-value text-danger">
                @php
                    $totalUsed = $scrapWasteRecords->sum(function($r) { return $r->materialStockOutLine->quantity_consumed ?? 0; });
                    $totalScrap = $scrapWasteRecords->sum('quantity');
                    $ratio = $totalUsed > 0 ? ($totalScrap / $totalUsed) * 100 : 0;
                @endphp
                {{ number_format($ratio, 1) }}%
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

    <!-- ─── FORM & INFO PANEL ─── -->
    <div class="bx-grid-2-1">
        <!-- Form -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ $isEditing ? 'Edit Scrap/Waste Record' : 'New Scrap/Waste Record' }}
                </h3>
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form">
                    <!-- Date -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Date</label>
                        <input type="date" wire:model="date"
                               class="bx-input @error('date') bx-input-error @enderror" />
                        @error('date')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Stock Out Batch -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Stock Out Batch</label>
                        <select wire:model="material_stock_out_id"
                                class="bx-select @error('material_stock_out_id') bx-input-error @enderror">
                            <option value="">Select Stock Out Batch</option>
                            @foreach ($stockOuts as $stockOut)
                                <option value="{{ $stockOut->id }}">
                                    Batch #{{ $stockOut->batch_number }} ({{ $stockOut->quantity }}kg)
                                </option>
                            @endforeach
                        </select>
                        @error('material_stock_out_id')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Production Line -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Production Line</label>
                        <select wire:model="production_line_id"
                                class="bx-select @error('production_line_id') bx-input-error @enderror">
                            <option value="">Select Production Line</option>
                            @foreach ($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                        @error('production_line_id')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Quantity Used -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Quantity Used (kg)</label>
                        <input type="number" wire:model="quantity_used" step="0.001" min="0.001"
                               class="bx-input @error('quantity_used') bx-input-error @enderror"
                               placeholder="Enter quantity used in kg" />
                        @error('quantity_used')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Scrap/Waste Quantity -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Scrap/Waste Quantity (kg)</label>
                        <input type="number" wire:model="quantity" step="0.001" min="0.001"
                               class="bx-input @error('quantity') bx-input-error @enderror"
                               placeholder="Enter waste quantity in kg" />
                        @error('quantity')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Reason</label>
                        <input type="text" wire:model="reason"
                               class="bx-input @error('reason') bx-input-error @enderror"
                               placeholder="Enter reason" />
                        @error('reason')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="bx-input @error('notes') bx-input-error @enderror"
                                  placeholder="Additional details about the waste (optional)"></textarea>
                        @error('notes')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="bx-form-group bx-form-full">
                        @if($isEditing)
                            <div class="flex gap-3">
                                <button type="submit" class="bx-btn bx-btn-warning flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Update Record
                                </button>
                                <button type="button" class="bx-btn bx-btn-secondary" wire:click="cancel">
                                    Cancel
                                </button>
                            </div>
                        @else
                            <button type="submit" class="bx-btn bx-btn-warning">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Record Scrap/Waste
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
                    Waste Management Guidelines
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-info-list">
                    <div class="bx-info-item bx-info-warning">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4>Daily Recording</h4>
                            <p>Record all scrap and waste materials daily</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-blue">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Waste Analysis</h4>
                            <p>Track waste ratios vs acceptable standards</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-danger">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Quality Control</h4>
                            <p>Identify and address quality issues promptly</p>
                        </div>
                    </div>
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Common Waste Reasons</h4>
                <div class="bx-tag-list">
                    <span class="bx-tag bx-tag-danger">Production Error</span>
                    <span class="bx-tag bx-tag-danger">Quality Issue</span>
                    <span class="bx-tag bx-tag-danger">Machine Malfunction</span>
                    <span class="bx-tag bx-tag-danger">Material Defect</span>
                    <span class="bx-tag bx-tag-danger">Process Waste</span>
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Waste Ratio Standards</h4>
                <div class="bx-standards">
                    <div class="bx-standard bx-standard-success">
                        <span class="bx-standard-label">Acceptable Waste</span>
                        <span class="bx-standard-value">≤ 2%</span>
                        <span class="bx-standard-desc">of total production</span>
                    </div>
                    <div class="bx-standard bx-standard-warning">
                        <span class="bx-standard-label">Warning Level</span>
                        <span class="bx-standard-value">2-5%</span>
                        <span class="bx-standard-desc">requires investigation</span>
                    </div>
                    <div class="bx-standard bx-standard-danger">
                        <span class="bx-standard-label">Critical Level</span>
                        <span class="bx-standard-value">> 5%</span>
                        <span class="bx-standard-desc">immediate action needed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── RECORDS TABLE ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Scrap/Waste Records</h2>
            <span class="bx-badge bx-badge-secondary">{{ $scrapWasteRecords->total() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Stock Out Batch</th>
                            <th class="hidden sm:table-cell">Line</th>
                            <th class="hidden md:table-cell">Qty Used</th>
                            <th>Scrap Qty</th>
                            <th class="hidden lg:table-cell">Reason</th>
                            <th class="hidden xl:table-cell">Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($scrapWasteRecords as $record)
                            <tr>
                                <td>{{ $record->waste_date ? \Carbon\Carbon::parse($record->waste_date)->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <span class="bx-code">{{ $record->materialStockOutLine->materialStockOut->batch_number ?? '-' }}</span>
                                </td>
                                <td class="hidden sm:table-cell">{{ $record->materialStockOutLine->productionLine->name ?? '-' }}</td>
                                <td class="hidden md:table-cell">{{ number_format($record->materialStockOutLine->quantity_consumed ?? 0, 2) }}</td>
                                <td>
                                    <span class="bx-badge bx-badge-danger">{{ number_format($record->quantity, 2) }} kg</span>
                                </td>
                                <td class="hidden lg:table-cell">
                                    <span class="bx-badge bx-badge-warning">{{ $record->reason }}</span>
                                </td>
                                <td class="hidden xl:table-cell">{{ Str::limit($record->notes ?? '-', 20) }}</td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $record->id }})"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $record->id }})"
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
                                <td colspan="8" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                        <h3>No records found</h3>
                                        <p>Start recording scrap and waste materials.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── PAGINATION ─── -->
        @if($scrapWasteRecords->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $scrapWasteRecords->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $scrapWasteRecords->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $scrapWasteRecords->total() }}</strong> entries
                </div>
                <div class="bx-pagination">
                    {{ $scrapWasteRecords->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
