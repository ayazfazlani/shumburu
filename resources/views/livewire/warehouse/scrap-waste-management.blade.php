<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-warning">🗑️ Scrap/Waste Management</h1>
                    <p class="text-base-content/70 mt-1">Manage raw material and finished goods scrap with proper categorization</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="create" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Scrap Record
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-warning">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Warehouse
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Success/Error Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- Scrap Type Tabs -->
        <div class="tabs tabs-boxed mb-6">
            <button class="tab tab-active" onclick="filterScrap('all')">All Scrap</button>
            <button class="tab" onclick="filterScrap('raw_material')">Raw Material Scrap</button>
            <button class="tab" onclick="filterScrap('finished_goods')">Finished Goods Scrap</button>
            <button class="tab" onclick="filterScrap('repressible')">Repressible Scrap</button>
        </div>

        <!-- Scrap Records Table -->
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Qty (kg)</th>
                        <th>Reason</th>
                        <th>Repressible</th>
                        <th>Disposal</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($scrapWasteRecords as $record)
                        <tr>
                            <td>{{ $record->waste_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="badge {{ $record->scrap_type === 'raw_material' ? 'badge-error' : 'badge-warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->scrap_type)) }}
                                </div>
                            </td>
                            <td>
                                @if($record->scrap_type === 'raw_material')
                                    {{ $record->materialStockOutLine->materialStockOut->rawMaterial->name ?? 'Unknown' }}
                                    <br><small class="text-gray-500">Line: {{ $record->materialStockOutLine->productionLine->name ?? '-' }}</small>
                                @else
                                    {{ $record->finishedGood->product->name ?? 'Unknown' }}
                                    <br><small class="text-gray-500">Size: {{ $record->finishedGood->size ?? '-' }}</small>
                                @endif
                            </td>
                            <td>{{ number_format($record->quantity, 2) }}</td>
                            <td>{{ $record->reason }}</td>
                            <td>
                                @if($record->is_repressible)
                                    <div class="badge badge-success">Yes</div>
                                @else
                                    <div class="badge badge-error">No</div>
                                @endif
                            </td>
                            <td>
                                <div class="badge badge-outline">{{ ucfirst($record->disposal_method) }}</div>
                            </td>
                            <td>{{ $record->cost ? '$' . number_format($record->cost, 2) : '-' }}</td>
                            <td>
                                @if($record->status === 'approved')
                                    <div class="badge badge-success">Approved</div>
                                @elseif($record->status === 'rejected')
                                    <div class="badge badge-error">Rejected</div>
                                @else
                                    <div class="badge badge-warning">Pending</div>
                                @endif
                            </td>
                            <td class="flex gap-1">
                                <button class="btn btn-xs btn-outline" wire:click="edit({{ $record->id }})">Edit</button>
                                @if($record->status === 'pending')
                                    <button class="btn btn-xs btn-success" wire:click="approve({{ $record->id }})">Approve</button>
                                    <button class="btn btn-xs btn-error" wire:click="reject({{ $record->id }})">Reject</button>
                                @endif
                                <button class="btn btn-xs btn-error" wire:click="delete({{ $record->id }})" 
                                        onclick="return confirm('Are you sure?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-gray-500 py-8">No scrap records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $scrapWasteRecords->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold mb-4">{{ $editingId ? 'Edit' : 'Add' }} Scrap/Waste Record</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Date *</label>
                            <input type="date" wire:model="date" class="input input-bordered w-full">
                            @error('date') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Scrap Type *</label>
                            <select wire:model="scrap_type" class="select select-bordered w-full">
                                <option value="raw_material">Raw Material Scrap</option>
                                <option value="finished_goods">Finished Goods Scrap</option>
                            </select>
                            @error('scrap_type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($scrap_type === 'raw_material')
                        <div>
                            <label class="label">Raw Material Source *</label>
                            <select wire:model="material_stock_out_line_id" class="select select-bordered w-full">
                                <option value="">Select Raw Material Source</option>
                                @foreach($stockOutLines as $line)
                                    <option value="{{ $line->id }}">
                                        {{ $line->materialStockOut->rawMaterial->name ?? 'Unknown' }} 
                                        (Line: {{ $line->productionLine->name ?? 'Unknown' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('material_stock_out_line_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div>
                            <label class="label">Finished Good *</label>
                            <select wire:model="finished_good_id" class="select select-bordered w-full">
                                <option value="">Select Finished Good</option>
                                @foreach($finishedGoods as $fg)
                                    <option value="{{ $fg->id }}">
                                        {{ $fg->product->name ?? 'Unknown' }} 
                                        (Size: {{ $fg->size ?? 'Unknown' }}, Qty: {{ $fg->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('finished_good_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Quantity (kg) *</label>
                            <input type="number" wire:model="quantity" step="0.001" min="0.001" 
                                   class="input input-bordered w-full" placeholder="Enter quantity">
                            @error('quantity') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Cost ($)</label>
                            <input type="number" wire:model="cost" step="0.01" min="0" 
                                   class="input input-bordered w-full" placeholder="Enter cost">
                            @error('cost') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label">Reason *</label>
                        <input type="text" wire:model="reason" class="input input-bordered w-full" 
                               placeholder="Enter reason for scrap">
                        @error('reason') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Is Repressible?</label>
                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <input type="checkbox" wire:model="is_repressible" class="checkbox">
                                    <span class="label-text ml-2">Can this scrap be reused?</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label class="label">Disposal Method *</label>
                            <select wire:model="disposal_method" class="select select-bordered w-full">
                                <option value="dispose">Dispose</option>
                                <option value="reprocess">Reprocess</option>
                                <option value="return_to_supplier">Return to Supplier</option>
                            </select>
                            @error('disposal_method') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label">Notes</label>
                        <textarea wire:model="notes" rows="3" class="textarea textarea-bordered w-full" 
                                  placeholder="Additional details about the scrap"></textarea>
                        @error('notes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="cancel" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $editingId ? 'Update' : 'Save' }} Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function filterScrap(type) {
    // This would be implemented with Livewire filters
    console.log('Filtering by:', type);
}
</script>
