<!-- resources/views/livewire/operations/downtime-record.blade.php -->
<div class="bx-page bx-page-downtime">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Downtime Record
            </h1>
            <p class="bx-header-subtitle">Track and record equipment downtime events</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-secondary">Today: {{ now()->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Downtime Events</div>
            <div class="bx-stat-value">{{ $totalEvents ?? 0 }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today's Downtime</div>
            <div class="bx-stat-value text-warning">{{ $todayEvents ?? 0 }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Duration (mins)</div>
            <div class="bx-stat-value text-danger">{{ $totalDuration ?? 0 }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Avg Duration (mins)</div>
            <div class="bx-stat-value text-blue">{{ $avgDuration ?? 0 }}</div>
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
    <div class="bx-grid-2-1">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $isEditing ? 'Edit Downtime Record' : 'New Downtime Record' }}
                </h3>
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form">
                    <!-- Date & Times -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Date</label>
                        <input type="date" wire:model="downtime_date"
                               class="bx-input @error('downtime_date') bx-input-error @enderror" />
                        @error('downtime_date')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label required">Start Time</label>
                        <input type="time" wire:model="start_time"
                               class="bx-input @error('start_time') bx-input-error @enderror" />
                        @error('start_time')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label required">End Time</label>
                        <input type="time" wire:model="end_time"
                               class="bx-input @error('end_time') bx-input-error @enderror" />
                        @error('end_time')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label">Duration (minutes)</label>
                        <input type="number" wire:model="duration_minutes"
                               class="bx-input bx-input-duration" readonly />
                        <span class="bx-helper-text bx-helper-text-duration">
                            <span class="bx-duration-icon">⏱️</span>
                            Auto-calculated from start and end times
                        </span>
                    </div>

                    <!-- Reason -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Reason</label>
                        <input type="text" wire:model="reason"
                               class="bx-input @error('reason') bx-input-error @enderror"
                               placeholder="Reason for downtime (e.g., Machine malfunction, Power outage)" />
                        @error('reason')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="bx-input @error('notes') bx-input-error @enderror"
                                  placeholder="Additional details about the downtime event..."></textarea>
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
                            <button type="submit" class="bx-btn bx-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Record Downtime
                            </button>
                            <button type="reset" wire:click="$refresh" class="bx-btn bx-btn-secondary">
                                Reset
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
                    Downtime Guidelines
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-info-list">
                    <div class="bx-info-item bx-info-warning">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4>Record All Downtime</h4>
                            <p>Log every equipment stoppage regardless of duration</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-blue">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Accurate Timing</h4>
                            <p>Start and end times are critical for analysis</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-danger">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Root Cause Analysis</h4>
                            <p>Identify patterns to prevent future downtime</p>
                        </div>
                    </div>
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Common Downtime Reasons</h4>
                <div class="bx-tag-list">
                    <span class="bx-tag bx-tag-warning">Machine Malfunction</span>
                    <span class="bx-tag bx-tag-warning">Power Outage</span>
                    <span class="bx-tag bx-tag-warning">Material Shortage</span>
                    <span class="bx-tag bx-tag-warning">Maintenance</span>
                    <span class="bx-tag bx-tag-warning">Operator Error</span>
                    <span class="bx-tag bx-tag-warning">Quality Issue</span>
                    <span class="bx-tag bx-tag-warning">Tool Change</span>
                    <span class="bx-tag bx-tag-warning">Break Time</span>
                </div>

                <div class="bx-divider"></div>

                <h4 class="bx-list-title">Downtime Impact</h4>
                <div class="bx-impact-stats">
                    <div class="bx-impact-item">
                        <span class="bx-impact-label">Cost per Minute</span>
                        <span class="bx-impact-value">${{ number_format($costPerMinute ?? 0, 2) }}</span>
                    </div>
                    <div class="bx-impact-item">
                        <span class="bx-impact-label">Production Loss</span>
                        <span class="bx-impact-value">{{ $productionLoss ?? 0 }} units</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── RECENT DOWNTIME RECORDS ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Recent Downtime Records</h2>
            <span class="bx-badge bx-badge-secondary">{{ $recentRecords->count() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th class="text-center">Duration (mins)</th>
                            <th>Reason</th>
                            <th class="hidden md:table-cell">Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRecords as $record)
                            <tr>
                                <td>{{ $record->downtime_date ? \Carbon\Carbon::parse($record->downtime_date)->format('M d, Y') : '-' }}</td>
                                <td>{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '-' }}</td>
                                <td>{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : '-' }}</td>
                                <td class="text-center font-mono font-bold">{{ $record->duration_minutes ?? 0 }}</td>
                                <td>
                                    <span class="bx-badge bx-badge-warning bx-badge-xs">{{ $record->reason }}</span>
                                </td>
                                <td class="hidden md:table-cell">{{ Str::limit($record->notes ?? '-', 25) }}</td>
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
                                <td colspan="7" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3>No downtime records found</h3>
                                        <p>Start logging equipment downtime events.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
