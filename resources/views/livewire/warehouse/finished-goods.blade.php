<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-4 md:py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-accent">📦 Finished Goods Management</h1>
                    <p class="text-base-content/70 mt-1 text-sm md:text-base">Complete CRUD operations for finished goods inventory</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 justify-center md:justify-end">
                    <button wire:click="create" class="btn btn-accent btn-sm sm:btn-md">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden sm:inline">Add Finished Good</span>
                        <span class="sm:hidden">Add</span>
                    </button>
                    <button wire:click="exportToCsv" class="btn btn-outline btn-success btn-sm sm:btn-md">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Export CSV</span>
                        <span class="sm:hidden">Export</span>
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-warning btn-sm sm:btn-md">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="hidden sm:inline">Back to Warehouse</span>
                        <span class="sm:hidden">Back</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-4 md:py-6">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success mb-4 md:mb-6">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm md:text-base">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-4 md:mb-6">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm md:text-base">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="card bg-base-100 shadow-lg mb-4 md:mb-6">
            <div class="card-body p-4 md:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
                    <div>
                        <label class="label text-sm md:text-base">Search</label>
                        <input type="text" wire:model.live="search" placeholder="Search by batch, size, notes..." 
                               class="input input-bordered w-full input-sm sm:input-sm">
                    </div>
                    <div>
                        <label class="label text-sm md:text-base">Product</label>
                        <select wire:model.live="filter_product" class="select select-bordered w-full select-sm sm:select-md">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label text-sm md:text-base">Customer</label>
                        <select wire:model.live="filter_customer" class="select select-bordered w-full select-sm sm:select-md">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label text-sm md:text-base">Purpose</label>
                        <select wire:model.live="filter_purpose" class="select select-bordered w-full select-sm sm:select-md">
                            <option value="">All Purposes</option>
                            <option value="for_stock">For Stock</option>
                            <option value="for_sale">For Sale</option>
                            <option value="for_customer">For Customer</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mb-4">
                    <div>
                        <label class="label text-sm md:text-base">Type</label>
                        <select wire:model.live="filter_type" class="select select-bordered w-full select-sm sm:select-md">
                            <option value="">All Types</option>
                            <option value="roll">Roll</option>
                            <option value="cut">Cut</option>
                        </select>
                    </div>
                    <div>
                        <label class="label text-sm md:text-base">Date From</label>
                        <input type="date" wire:model.live="date_from" class="input input-bordered w-full input-sm sm:input-md">
                    </div>
                    <div>
                        <label class="label text-sm md:text-base">Date To</label>
                        <input type="date" wire:model.live="date_to" class="input input-bordered w-full input-sm sm:input-md">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                    <div class="flex gap-2 justify-center md:justify-start">
                        <button wire:click="clearFilters" class="btn btn-outline btn-sm">Clear Filters</button>
                    </div>
                    <div class="flex items-center gap-2 justify-center md:justify-end">
                        <label class="label text-sm md:text-base">Per Page:</label>
                        <select wire:model.live="perPage" class="select select-bordered select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedItems) > 0)
            <div class="card bg-warning shadow-lg mb-4 md:mb-6">
                <div class="card-body p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <span class="font-semibold text-sm md:text-base">{{ count($selectedItems) }} items selected</span>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <select wire:model="bulkAction" class="select select-bordered select-sm sm:select-md">
                                    <option value="">Choose Action</option>
                                    <option value="for_stock">Mark as For Stock</option>
                                    <option value="for_sale">Mark as For Sale</option>
                                    <option value="for_customer">Mark as For Customer</option>
                                </select>
                                <button wire:click="updatePurpose" class="btn btn-sm sm:btn-md btn-primary">Update Purpose</button>
                                <button wire:click="deleteSelected" class="btn btn-sm sm:btn-md btn-error" 
                                        onclick="return confirm('Are you sure you want to delete selected items?')">
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                        <button wire:click="$set('selectedItems', [])" class="btn btn-sm sm:btn-md btn-outline">Clear Selection</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Finished Goods Table -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="text-xs md:text-sm">
                                <th>
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" class="checkbox checkbox-xs md:checkbox-sm">
                                    </label>
                                </th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Length (m)</th>
                                <th>Batch #</th>
                                <th class="hidden md:table-cell">Production Date</th>
                                <th>Purpose</th>
                                <th class="hidden lg:table-cell">Customer</th>
                                <th class="hidden lg:table-cell">Size</th>
                                <th class="hidden lg:table-cell">Weight (kg)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finishedGoods as $good)
                                <tr class="hover:bg-base-200 text-xs md:text-sm">
                                    <td>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" wire:model="selectedItems" value="{{ $good->id }}" class="checkbox checkbox-xs md:checkbox-sm">
                                        </label>
                                    </td>
                                    <td class="font-mono">{{ $good->id }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $good->product->name ?? 'Unknown' }}</div>
                                        @if($good->size)
                                            <div class="text-xs text-gray-500 md:hidden">{{ $good->size }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="badge badge-xs md:badge-sm {{ $good->type === 'roll' ? 'badge-primary' : 'badge-secondary' }}">
                                            {{ ucfirst($good->type) }}
                                        </div>
                                    </td>
                                    <td class="text-right">{{ number_format($good->quantity, 0) }}</td>
                                    <td class="text-right">{{ number_format($good->length_m, 2) }}</td>
                                    <td class="font-mono">{{ $good->batch_number ?? '-' }}</td>
                                    <td class="hidden md:table-cell">{{ $good->production_date->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="badge badge-outline badge-xs md:badge-sm">
                                            {{ ucfirst(str_replace('_', ' ', $good->purpose)) }}
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell">{{ $good->customer->name ?? '-' }}</td>
                                    <td class="hidden lg:table-cell">{{ $good->size ?? '-' }}</td>
                                    <td class="hidden lg:table-cell text-right">{{ $good->total_weight ? number_format($good->total_weight, 2) : '-' }}</td>
                                    <td>
                                        <div class="flex flex-col sm:flex-row gap-1">
                                            <button wire:click="view({{ $good->id }})" class="btn btn-xs sm:btn-sm btn-info">
                                                <span class="hidden sm:inline">View</span>
                                                <svg class="w-3 h-3 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $good->id }})" class="btn btn-xs sm:btn-sm btn-warning">
                                                <span class="hidden sm:inline">Edit</span>
                                                <svg class="w-3 h-3 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $good->id }})" class="btn btn-xs sm:btn-sm btn-error"
                                                    onclick="return confirm('Are you sure?')">
                                                <span class="hidden sm:inline">Delete</span>
                                                <svg class="w-3 h-3 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-gray-500 py-8 text-sm md:text-base">
                                        No finished goods found.
                                        @if($search || $filter_product || $filter_customer || $filter_purpose || $filter_type || $date_from || $date_to)
                                            <div class="mt-2">
                                                <button wire:click="clearFilters" class="btn btn-sm btn-outline">Clear filters</button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $finishedGoods->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showForm)
        <!-- Daisy UI Modal -->
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg md:text-xl font-bold mb-4">{{ $editingId ? 'Edit' : 'Add' }} Finished Good</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Product *</label>
                            <select wire:model="product_id" class="select select-bordered w-full select-sm sm:select-md">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Type *</label>
                            <select wire:model="type" class="select select-bordered w-full select-sm sm:select-md">
                                <option value="roll">Roll</option>
                                <option value="cut">Cut</option>
                            </select>
                            @error('type') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Quantity *</label>
                            <input type="number" wire:model="quantity" step="0.001" min="0.001" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter quantity">
                            @error('quantity') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Length (m) *</label>
                            <input type="number" wire:model="length_m" step="0.001" min="0.001" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter length">
                            @error('length_m') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Batch Number *</label>
                            <input type="text" wire:model="batch_number" class="input input-bordered w-full input-sm sm:input-md" 
                                   placeholder="Enter batch number">
                            @error('batch_number') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Production Date *</label>
                            <input type="date" wire:model="production_date" class="input input-bordered w-full input-sm sm:input-md">
                            @error('production_date') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Purpose *</label>
                            <select wire:model="purpose" class="select select-bordered w-full select-sm sm:select-md">
                                <option value="for_stock">For Stock</option>
                                <option value="for_sale">For Sale</option>
                                <option value="for_customer">For Customer</option>
                            </select>
                            @error('purpose') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Customer</label>
                            <select wire:model="customer_id" class="select select-bordered w-full select-sm sm:select-md">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Size</label>
                            <input type="text" wire:model="size" class="input input-bordered w-full input-sm sm:input-md" 
                                   placeholder="Enter size">
                            @error('size') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Outer Diameter</label>
                            <input type="number" wire:model="outer_diameter" step="0.001" min="0" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter diameter">
                            @error('outer_diameter') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Thickness</label>
                            <input type="number" wire:model="thickness" step="0.001" min="0" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter thickness">
                            @error('thickness') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Surface</label>
                            <select wire:model="surface" class="select select-bordered w-full select-sm sm:select-md">
                                <option value="">Select Surface</option>
                                <option value="smooth">Smooth</option>
                                <option value="rough">Rough</option>
                            </select>
                            @error('surface') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Start Ovality</label>
                            <input type="number" wire:model="start_ovality" step="0.001" min="0" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter start ovality">
                            @error('start_ovality') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">End Ovality</label>
                            <input type="number" wire:model="end_ovality" step="0.001" min="0" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Enter end ovality">
                            @error('end_ovality') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Stripe Color</label>
                            <input type="text" wire:model="stripe_color" class="input input-bordered w-full input-sm sm:input-md" 
                                   placeholder="Enter stripe color">
                            @error('stripe_color') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label text-sm md:text-base">Total Weight (kg)</label>
                            <input type="number" wire:model="total_weight" step="0.001" min="0" 
                                   class="input input-bordered w-full input-sm sm:input-md" placeholder="Auto-calculated if empty">
                            @error('total_weight') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label text-sm md:text-base">Notes</label>
                        <textarea wire:model="notes" rows="3" class="textarea textarea-bordered w-full textarea-sm sm:textarea-md" 
                                  placeholder="Additional notes"></textarea>
                        @error('notes') <span class="text-error text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline btn-sm sm:btn-md">Cancel</button>
                        <button type="submit" class="btn btn-accent btn-sm sm:btn-md">
                            {{ $editingId ? 'Update' : 'Save' }} Finished Good
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if($viewingId)
        @php
            $good = \App\Models\FinishedGood::with(['product', 'customer', 'producedBy'])->find($viewingId);
        @endphp
        @if($good)
            <!-- Daisy UI Modal -->
            <div class="modal modal-open">
                <div class="modal-box w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg md:text-xl font-bold mb-4">Finished Good Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="label text-sm md:text-base">Product</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->product->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Type</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ ucfirst($good->type) }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Quantity</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ number_format($good->quantity, 0) }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Length (m)</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ number_format($good->length_m, 2) }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Batch Number</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->batch_number ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Production Date</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->production_date->format('Y-m-d') }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Purpose</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ ucfirst(str_replace('_', ' ', $good->purpose)) }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Customer</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->customer->name ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Size</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->size ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Outer Diameter</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->outer_diameter ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Surface</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->surface ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Thickness</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->thickness ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Start Ovality</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->start_ovality ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">End Ovality</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->end_ovality ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Stripe Color</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->stripe_color ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Total Weight (kg)</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->total_weight ? number_format($good->total_weight, 2) : '-' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Produced By</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->producedBy->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="label text-sm md:text-base">Created At</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                    
                    @if($good->notes)
                        <div class="mt-4">
                            <label class="label text-sm md:text-base">Notes</label>
                            <div class="p-2 bg-base-200 rounded text-sm md:text-base">{{ $good->notes }}</div>
                        </div>
                    @endif

                    <div class="modal-action">
                        <button wire:click="$set('viewingId', null)" class="btn btn-outline btn-sm sm:btn-md">Close</button>
                        <button wire:click="edit({{ $good->id }})" class="btn btn-accent btn-sm sm:btn-md">Edit</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>