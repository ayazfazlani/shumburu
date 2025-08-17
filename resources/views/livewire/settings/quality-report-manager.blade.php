<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Quality Report Manager</h2>
        <button wire:click="create" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Quality Report
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Form Modal -->
    @if($showForm)
        <dialog id="quality-report-modal" class="modal" open>
            <form method="dialog" class="modal-box w-full max-w-4xl max-h-screen overflow-y-auto" wire:submit.prevent="save">
                <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit' : 'Create' }} Quality Report</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="label">Report Type</label>
                            <select wire:model="report_type" class="input input-bordered w-full">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            @error('report_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="label">Start Date</label>
                            <input type="date" wire:model="start_date" class="input input-bordered w-full">
                            @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="label">End Date</label>
                            <input type="date" wire:model="end_date" class="input input-bordered w-full">
                            @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="label">Quality Comment</label>
                        <textarea wire:model="quality_comment" class="textarea textarea-bordered w-full" rows="3" 
                                  placeholder="Enter quality comment..."></textarea>
                        @error('quality_comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="label">Problems</label>
                        <textarea wire:model="problems" class="textarea textarea-bordered w-full" rows="4" 
                                  placeholder="List the problems observed..."></textarea>
                        @error('problems') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="label">Corrective Actions</label>
                        <textarea wire:model="corrective_actions" class="textarea textarea-bordered w-full" rows="4" 
                                  placeholder="Describe corrective actions taken..."></textarea>
                        @error('corrective_actions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="label">Remarks</label>
                        <textarea wire:model="remarks" class="textarea textarea-bordered w-full" rows="3" 
                                  placeholder="Additional remarks..."></textarea>
                        @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="label">Prepared By</label>
                            <input type="text" wire:model="prepared_by" class="input input-bordered w-full" 
                                   placeholder="Name of preparer">
                            @error('prepared_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="label">Checked By</label>
                            <input type="text" wire:model="checked_by" class="input input-bordered w-full" 
                                   placeholder="Name of checker">
                            @error('checked_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="label">Approved By</label>
                            <input type="text" wire:model="approved_by" class="input input-bordered w-full" 
                                   placeholder="Name of approver">
                            @error('approved_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="label cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="checkbox">
                            <span class="label-text ml-2">Active</span>
                        </label>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="cancel" class="btn btn-ghost">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </dialog>
        @endif

    <!-- Quality Reports Table -->
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Report Type</th>
                    <th>Period</th>
                    <th>Quality Comment</th>
                    <th>Problems</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qualityReports as $report)
                    <tr>
                        <td class="capitalize">{{ $report->report_type }}</td>
                        <td>
                            {{ $report->start_date->format('M d, Y') }} - 
                            {{ $report->end_date->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="max-w-xs truncate" title="{{ $report->quality_comment }}">
                                {{ Str::limit($report->quality_comment, 50) }}
                            </div>
                        </td>
                        <td>
                            <div class="max-w-xs truncate" title="{{ $report->problems }}">
                                {{ Str::limit($report->problems, 50) }}
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $report->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $report->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-right flex gap-2 justify-end">
                            <button wire:click="edit({{ $report->id }})" class="btn btn-sm btn-outline">Edit</button>
                            <button wire:click="confirmDelete({{ $report->id }})" 
                                    class="btn btn-sm btn-error">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-6">No quality reports found. Click "Add Quality Report" to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $qualityReports->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <dialog id="delete-modal" class="modal" open>
            <form method="dialog" class="modal-box">
                <h3 class="font-bold text-lg">Delete Quality Report</h3>
                <p class="py-4">Are you sure you want to delete this quality report? This action cannot be undone.</p>
                <div class="modal-action">
                    <button type="button" wire:click="cancelDelete" class="btn btn-ghost">Cancel</button>
                    <button type="button" wire:click="deleteConfirmed" class="btn btn-error">Delete</button>
                </div>
            </form>
        </dialog>
    @endif
</div> 