<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📥 Material Stock In</h1>
                    <p class="text-base-content/70 mt-1">Record incoming raw materials to warehouse</p>
                </div>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Warehouse
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Success/Error Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Stock In Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary mb-6">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            @if($is_editing) Edit Stock In Record @else New Stock In Record @endif
                        </h2>

                        <form wire:submit.prevent="save" class="space-y-6">
                            <!-- Raw Material Selection -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Raw Material *</span>
                                </label>
                                <select wire:model="raw_material_id"
                                    class="select select-bordered w-full @error('raw_material_id') select-error @enderror">
                                    <option value="">Select Raw Material</option>
                                    @foreach ($rawMaterials as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }}
                                            ({{ $material->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('raw_material_id')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Quantity (kg) *</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" wire:model="quantity" step="0.001" min="0.001"
                                        class="input input-bordered w-full @error('quantity') input-error @enderror"
                                        placeholder="Enter quantity in kg">
                                    <span class="btn btn-square btn-outline">kg</span>
                                </div>
                                @error('quantity')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Batch Number -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Batch Number *</span>
                                </label>
                                <input type="text" wire:model="batch_number"
                                    class="input input-bordered w-full @error('batch_number') input-error @enderror"
                                    placeholder="Enter batch number">
                                @error('batch_number')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Received Date -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Received Date *</span>
                                </label>
                                <input type="date" wire:model="received_date"
                                    class="input input-bordered w-full @error('received_date') input-error @enderror">
                                @error('received_date')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Notes</span>
                                </label>
                                <textarea wire:model="notes" rows="3" class="textarea textarea-bordered @error('notes') textarea-error @enderror"
                                    placeholder="Additional notes (optional)"></textarea>
                                @error('notes')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-control pt-4">
                                @if($is_editing)
                                <div class="flex space-x-3">
                                    <button type="submit" class="btn btn-primary btn-lg flex-1">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Update Record
                                    </button>
                                    <button type="button" wire:click="cancelEdit" class="btn btn-outline btn-error">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>
                                </div>
                                @else
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Record Stock In
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-info">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Stock In Guidelines
                        </h3>
                        <div class="space-y-4">
                            <div class="alert alert-info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Material Types</h4>
                                    <p class="text-sm">We handle 7 types of raw materials for HDPE pipe production</p>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Batch Tracking</h4>
                                    <p class="text-sm">Each batch must have a unique batch number for traceability</p>
                                </div>
                            </div>

                            <div class="alert alert-success">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Quality Control</h4>
                                    <p class="text-sm">All materials are checked for quality before stock-in</p>
                                </div>
                            </div>
                        </div>

                        <!-- Raw Materials List -->
                        <div class="divider"></div>
                        <h4 class="font-semibold mb-3">Available Raw Materials</h4>
                        <div class="space-y-2">
                            @foreach ($rawMaterials as $material)
                                <div class="flex items-center justify-between p-2 bg-base-200 rounded-lg">
                                    <div>
                                        <div class="font-medium text-sm">{{ $material->name }}</div>
                                        <div class="text-xs opacity-70">{{ $material->code }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="badge badge-outline badge-sm">{{ $material->unit }}</div>
                                        <div class="badge badge-sm {{ $material->quantity > 0 ? 'badge-success' : 'badge-warning' }}">
                                            {{ number_format($material->quantity, 1) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-primary">Stock In Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="py-3 px-4">Raw Material</th>
                        <th class="py-3 px-4">Quantity</th>
                        <th class="py-3 px-4">Batch No</th>
                        <th class="py-3 px-4">Received Date</th>
                        <th class="py-3 px-4">Received By</th>
                        <th class="py-3 px-4">Notes</th>
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockIns as $item)
                        <tr>
                            <td class="py-3 px-4">{{ $item->rawMaterial->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ number_format($item->quantity, 2) }} kg</td>
                            <td class="py-3 px-4">
                                <span class="badge badge-outline whitespace-nowrap">{{ $item->batch_number }}</span>
                            </td>
                            <td class="py-3 px-4">{{ Carbon\Carbon::parse($item->received_date)->format('d-m-Y') }}</td>
                            <td class="py-3 px-4">{{ $item->receivedBy->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ $item->notes ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $item->id }})" class="btn btn-xs btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button onclick="document.getElementById('delete_modal_{{$item->id}}').showModal()" 
                                            class="btn btn-xs btn-error">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <dialog id="delete_modal_{{$item->id}}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Confirm Delete</h3>
                                        <p class="py-4">Are you sure you want to delete this stock in record? This action will deduct {{ number_format($item->quantity, 2) }} kg from {{ $item->rawMaterial->name ?? 'N/A' }} stock.</p>
                                        <div class="modal-action">
                                            <form method="dialog">
                                                <button class="btn btn-ghost">Cancel</button>
                                            </form>
                                            <button wire:click="setDeleteId({{ $item->id }})"
                                                onclick="document.getElementById('delete_modal_{{$item->id}}').close(); setTimeout(() => { $wire.delete() }, 100)"
                                                class="btn btn-error">Delete</button>
                                        </div>
                                    </div>
                                </dialog>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-base-content/70">
                    Showing {{ $stockIns->firstItem() ?? 0 }} to {{ $stockIns->lastItem() ?? 0 }} of {{ $stockIns->total() }} entries
                </div>
            </div>

             <div class="join">
                    {{ $stockIns->links('components.pagination') }}
             </div>
        </div>
    </div>
</div>

@script
<script>
    // Scroll to form when editing
    document.addEventListener('livewire:initialized', () => {
        $wire.on('scroll-to-form', () => {
            document.querySelector('form').scrollIntoView({ 
                behavior: 'smooth' 
            });
        });
    });
</script>
@endscript
