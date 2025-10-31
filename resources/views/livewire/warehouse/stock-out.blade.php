<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary">📤 Material Stock Out</h1>
                    <p class="text-base-content/70 mt-1">Issue raw materials to production line</p>
                </div>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-secondary">
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
            <!-- Stock Out Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-secondary mb-6">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            @if($is_editing) Edit Stock Out Record @else New Stock Out Record @endif
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
                                    <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }}) -
                                        {{ number_format($material->quantity, 3) }} {{ $material->unit }}</option>
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

                            <!-- Issued Date -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Issued Date *</span>
                                </label>
                                <input type="date" wire:model="issued_date"
                                    class="input input-bordered w-full @error('issued_date') input-error @enderror">
                                @error('issued_date')
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
                                <textarea wire:model="notes" rows="3"
                                    class="textarea textarea-bordered @error('notes') textarea-error @enderror"
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
                                <button type="submit" class="btn btn-secondary btn-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Issue Material
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
                            Stock Out Guidelines
                        </h3>
                        <div class="space-y-4">
                            <div class="alert alert-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Material Status</h4>
                                    <p class="text-sm">Issued materials are tagged as "Material on Process"</p>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Production Line</h4>
                                    <p class="text-sm">Materials are issued to production line for HDPE pipe
                                        manufacturing</p>
                                </div>
                            </div>

                            <div class="alert alert-success">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-bold">Tracking</h4>
                                    <p class="text-sm">All issued materials are tracked until completion</p>
                                </div>
                            </div>
                        </div>

                        <!-- Status Information -->
                        <div class="divider"></div>
                        <h4 class="font-semibold mb-3">Material Status Flow</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="badge badge-warning">On Process</div>
                                <span class="text-sm">Material issued to production</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="badge badge-success">Completed</div>
                                <span class="text-sm">Production finished successfully</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="badge badge-error">Scrapped</div>
                                <span class="text-sm">Material became waste</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Out Records Table -->
    <div class="container mx-auto px-4 py-6">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-6">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Stock Out History
                </h2>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Raw Material</th>
                                <th>Quantity</th>
                                <th>Batch No</th>
                                <th>Issued Date</th>
                                <th>Issued By</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockOuts as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->rawMaterial->name }}</td>
                                <td>{{ number_format($item->quantity, 3) }} kg</td>
                                <td>{{ $item->batch_number }}</td>
                                <td>{{ $item->issued_date }}</td>
                                <td>{{ $item->issuedBy->name }}</td>
                                <td>
                                    <div class="dropdown dropdown-end">
                                        <label tabindex="0"
                                            class="badge badge-{{ $item->status == 'material_on_process' ? 'warning' : ($item->status == 'completed' ? 'success' : 'error') }} cursor-pointer">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </label>
                                        <ul tabindex="0"
                                            class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li><a wire:click="updateStatus({{ $item->id }}, 'material_on_process')">Material
                                                    on Process</a></li>
                                            <li><a wire:click="updateStatus({{ $item->id }}, 'completed')">Completed</a>
                                            </li>
                                            <li><a wire:click="updateStatus({{ $item->id }}, 'scrapped')">Scrapped</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $item->notes ?? '-' }}</td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button wire:click="edit({{ $item->id }})"
                                            class="btn btn-sm btn-outline btn-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            onclick="document.getElementById('delete_modal_{{$item->id}}').showModal()"
                                            class="btn btn-sm btn-outline btn-error">
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
                                            <p class="py-4">Are you sure you want to delete this stock out record? This
                                                action will restore {{ number_format($item->quantity, 3) }} kg back to
                                                {{ $item->rawMaterial->name }} stock.</p>
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
                </div>

                <div class="mt-4">
                    {{ $stockOuts->links('components.pagination') }}
                </div>
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