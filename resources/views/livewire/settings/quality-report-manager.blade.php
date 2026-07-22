<!-- resources/views/livewire/settings/quality-reports.blade.php -->
<div class="bx-page bx-page-quality">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Quality Report Manager
            </h1>
            <p class="bx-header-subtitle">Create and manage quality inspection reports</p>
        </div>
        <div class="bx-header-right">
            <button wire:click="create" class="bx-btn bx-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Quality Report
            </button>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Reports</div>
            <div class="bx-stat-value">{{ $qualityReports->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active</div>
            <div class="bx-stat-value text-green">{{ $qualityReports->where('is_active', true)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Inactive</div>
            <div class="bx-stat-value text-gray">{{ $qualityReports->where('is_active', false)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">This Month</div>
            <div class="bx-stat-value text-blue">{{ $qualityReports->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
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

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Report Type</th>
                        <th>Period</th>
                        <th class="hidden md:table-cell">Quality Comment</th>
                        <th class="hidden lg:table-cell">Problems</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($qualityReports as $report)
                        <tr>
                            <td>
                                <span class="bx-badge bx-badge-info">{{ ucfirst($report->report_type) }}</span>
                            </td>
                            <td>
                                <span class="bx-code">{{ $report->start_date->format('M d, Y') }} - {{ $report->end_date->format('M d, Y') }}</span>
                            </td>
                            <td class="hidden md:table-cell">
                                <div class="bx-truncate" title="{{ $report->quality_comment }}">
                                    {{ Str::limit($report->quality_comment, 50) }}
                                </div>
                            </td>
                            <td class="hidden lg:table-cell">
                                <div class="bx-truncate" title="{{ $report->problems }}">
                                    {{ Str::limit($report->problems, 50) }}
                                </div>
                            </td>
                            <td>
                                @if($report->is_active)
                                    <span class="bx-badge bx-badge-success">Active</span>
                                @else
                                    <span class="bx-badge bx-badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="edit({{ $report->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $report->id }})"
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
                            <td colspan="6" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3>No quality reports found</h3>
                                    <p>Click "Add Quality Report" to create your first report.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($qualityReports->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $qualityReports->firstItem() ?? 0 }}</strong>
                to <strong>{{ $qualityReports->lastItem() ?? 0 }}</strong>
                of <strong>{{ $qualityReports->total() }}</strong> reports
            </div>
            <div class="bx-pagination">
                {{ $qualityReports->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showForm)
        <div class="bx-modal-overlay" wire:click.self="cancel">
            <div class="bx-modal bx-modal-lg">
                <form wire:submit.prevent="save">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $editingId ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $editingId ? 'Edit' : 'Create' }} Quality Report
                        </h3>
                        <button type="button" wire:click="cancel" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Report Type -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Report Type</label>
                                <select wire:model="report_type" class="bx-select @error('report_type') bx-input-error @enderror">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                                @error('report_type')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Start Date</label>
                                <input type="date" wire:model="start_date" class="bx-input @error('start_date') bx-input-error @enderror" />
                                @error('start_date')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">End Date</label>
                                <input type="date" wire:model="end_date" class="bx-input @error('end_date') bx-input-error @enderror" />
                                @error('end_date')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Quality Comment -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Quality Comment</label>
                                <textarea wire:model="quality_comment" rows="3"
                                          class="bx-input @error('quality_comment') bx-input-error @enderror"
                                          placeholder="Enter quality comment..."></textarea>
                                @error('quality_comment')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Problems -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Problems</label>
                                <textarea wire:model="problems" rows="4"
                                          class="bx-input @error('problems') bx-input-error @enderror"
                                          placeholder="List the problems observed..."></textarea>
                                @error('problems')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Corrective Actions -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Corrective Actions</label>
                                <textarea wire:model="corrective_actions" rows="4"
                                          class="bx-input @error('corrective_actions') bx-input-error @enderror"
                                          placeholder="Describe corrective actions taken..."></textarea>
                                @error('corrective_actions')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remarks -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Remarks</label>
                                <textarea wire:model="remarks" rows="3"
                                          class="bx-input @error('remarks') bx-input-error @enderror"
                                          placeholder="Additional remarks..."></textarea>
                                @error('remarks')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Prepared By -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Prepared By</label>
                                <input type="text" wire:model="prepared_by"
                                       class="bx-input @error('prepared_by') bx-input-error @enderror"
                                       placeholder="Name of preparer" />
                                @error('prepared_by')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Checked By -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Checked By</label>
                                <input type="text" wire:model="checked_by"
                                       class="bx-input @error('checked_by') bx-input-error @enderror"
                                       placeholder="Name of checker" />
                                @error('checked_by')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Approved By -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Approved By</label>
                                <input type="text" wire:model="approved_by"
                                       class="bx-input @error('approved_by') bx-input-error @enderror"
                                       placeholder="Name of approver" />
                                @error('approved_by')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Active -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-checkbox">
                                    <input type="checkbox" wire:model="is_active" />
                                    <span>Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="cancel" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $editingId ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELETE MODAL ─── -->
    @if($showDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="cancelDelete">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Quality Report
                    </h3>
                    <button type="button" wire:click="cancelDelete" class="bx-modal-close">
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the quality report.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="cancelDelete" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteConfirmed" class="bx-btn bx-btn-danger">
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
