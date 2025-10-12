<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📦 Finished Goods Management</h1>
                    <p class="text-base-content/70 mt-1">Complete CRUD operations for finished goods inventory</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="create" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Finished Good
                    </button>
                    <button wire:click="exportToCsv" class="btn btn-outline btn-success">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
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
        @if (session()->has('success'))
            <div class="alert alert-success mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="card bg-base-100 shadow-lg mb-6">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="label">Search</label>
                        <input type="text" wire:model.live="search" placeholder="Search by batch, size, notes..." 
                               class="input input-bordered w-full">
                    </div>
                    <div>
                        <label class="label">Product</label>
                        <select wire:model.live="filter_product" class="select select-bordered w-full">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Customer</label>
                        <select wire:model.live="filter_customer" class="select select-bordered w-full">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Purpose</label>
                        <select wire:model.live="filter_purpose" class="select select-bordered w-full">
                            <option value="">All Purposes</option>
                            <option value="for_stock">For Stock</option>
                            <option value="for_sale">For Sale</option>
                            <option value="for_customer">For Customer</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="label">Type</label>
                        <select wire:model.live="filter_type" class="select select-bordered w-full">
                            <option value="">All Types</option>
                            <option value="roll">Roll</option>
                            <option value="cut">Cut</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Date From</label>
                        <input type="date" wire:model.live="date_from" class="input input-bordered w-full">
                    </div>
                    <div>
                        <label class="label">Date To</label>
                        <input type="date" wire:model.live="date_to" class="input input-bordered w-full">
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex gap-2">
                        <button wire:click="clearFilters" class="btn btn-outline btn-sm">Clear Filters</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="label">Per Page:</label>
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
            <div class="card bg-warning shadow-lg mb-6">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="font-semibold">{{ count($selectedItems) }} items selected</span>
                            <select wire:model="bulkAction" class="select select-bordered select-sm">
                                <option value="">Choose Action</option>
                                <option value="for_stock">Mark as For Stock</option>
                                <option value="for_sale">Mark as For Sale</option>
                                <option value="for_customer">Mark as For Customer</option>
                            </select>
                            <button wire:click="updatePurpose" class="btn btn-sm btn-primary">Update Purpose</button>
                            <button wire:click="deleteSelected" class="btn btn-sm btn-error" 
                                    onclick="return confirm('Are you sure you want to delete selected items?')">
                                Delete Selected
                            </button>
                        </div>
                        <button wire:click="$set('selectedItems', [])" class="btn btn-sm btn-outline">Clear Selection</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Finished Goods Table -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" class="checkbox checkbox-sm">
                                    </label>
                                </th>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Batch #</th>
                                <th>Production Date</th>
                                <th>Purpose</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Length (m)</th>
                                <th>Weight (kg)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finishedGoods as $good)
                                <tr class="hover:bg-base-200">
                                    <td>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" wire:model="selectedItems" value="{{ $good->id }}" class="checkbox checkbox-sm">
                                        </label>
                                    </td>
                                    <td class="font-mono text-sm">{{ $good->id }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $good->product->name ?? 'Unknown' }}</div>
                                        @if($good->size)
                                            <div class="text-sm text-gray-500">{{ $good->size }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($good->quantity, 0) }}</td>
                                    <td class="font-mono text-sm">{{ $good->batch_number ?? '-' }}</td>
                                    <td>{{ $good->production_date->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="badge badge-outline">
                                            {{ ucfirst(str_replace('_', ' ', $good->purpose)) }}
                                        </div>
                                    </td>
                                    <td>{{ $good->customer->name ?? '-' }}</td>
                                    <td>
                                        <div class="badge badge-sm {{ $good->type === 'roll' ? 'badge-primary' : 'badge-secondary' }}">
                                            {{ ucfirst($good->type) }}
                                        </div>
                                    </td>
                                    <td>{{ $good->size ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($good->length_m, 2) }}</td>
                                    <td class="text-right">{{ $good->total_weight ? number_format($good->total_weight, 2) : '-' }}</td>
                                    <td>
                                        <div class="flex gap-1">
                                            <button wire:click="view({{ $good->id }})" class="btn btn-xs btn-info">View</button>
                                            <button wire:click="edit({{ $good->id }})" class="btn btn-xs btn-warning">Edit</button>
                                            <button wire:click="delete({{ $good->id }})" class="btn btn-xs btn-error"
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-gray-500 py-8">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold mb-4">{{ $editingId ? 'Edit' : 'Add' }} Finished Good</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Product *</label>
                            <select wire:model="product_id" class="select select-bordered w-full">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Quantity *</label>
                            <input type="number" wire:model="quantity" step="0.001" min="0.001" 
                                   class="input input-bordered w-full" placeholder="Enter quantity">
                            @error('quantity') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Batch Number</label>
                            <input type="text" wire:model="batch_number" class="input input-bordered w-full" 
                                   placeholder="Enter batch number">
                            @error('batch_number') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Production Date *</label>
                            <input type="date" wire:model="production_date" class="input input-bordered w-full">
                            @error('production_date') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Purpose *</label>
                            <select wire:model="purpose" class="select select-bordered w-full">
                                <option value="for_stock">For Stock</option>
                                <option value="for_sale">For Sale</option>
                                <option value="for_customer">For Customer</option>
                            </select>
                            @error('purpose') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Customer</label>
                            <select wire:model="customer_id" class="select select-bordered w-full">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Type *</label>
                            <select wire:model="type" class="select select-bordered w-full">
                                <option value="roll">Roll</option>
                                <option value="cut">Cut</option>
                            </select>
                            @error('type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Length (m) *</label>
                            <input type="number" wire:model="length_m" step="0.001" min="0.001" 
                                   class="input input-bordered w-full" placeholder="Enter length">
                            @error('length_m') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="label">Size</label>
                            <input type="text" wire:model="size" class="input input-bordered w-full" 
                                   placeholder="Enter size">
                            @error('size') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Outer Diameter</label>
                            <input type="number" wire:model="outer_diameter" step="0.001" min="0" 
                                   class="input input-bordered w-full" placeholder="Enter diameter">
                            @error('outer_diameter') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Thickness</label>
                            <input type="number" wire:model="thickness" step="0.001" min="0" 
                                   class="input input-bordered w-full" placeholder="Enter thickness">
                            @error('thickness') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="label">Surface</label>
                            <input type="text" wire:model="surface" class="input input-bordered w-full" 
                                   placeholder="Enter surface">
                            @error('surface') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Start Ovality</label>
                            <input type="number" wire:model="start_ovality" step="0.001" min="0" 
                                   class="input input-bordered w-full" placeholder="Enter start ovality">
                            @error('start_ovality') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">End Ovality</label>
                            <input type="number" wire:model="end_ovality" step="0.001" min="0" 
                                   class="input input-bordered w-full" placeholder="Enter end ovality">
                            @error('end_ovality') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Stripe Color</label>
                            <input type="text" wire:model="stripe_color" class="input input-bordered w-full" 
                                   placeholder="Enter stripe color">
                            @error('stripe_color') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="label">Total Weight (kg)</label>
                            <input type="number" wire:model="total_weight" step="0.001" min="0" 
                                   class="input input-bordered w-full" placeholder="Auto-calculated if empty">
                            @error('total_weight') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label">Notes</label>
                        <textarea wire:model="notes" rows="3" class="textarea textarea-bordered w-full" 
                                  placeholder="Additional notes"></textarea>
                        @error('notes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-primary">
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
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <h3 class="text-xl font-bold mb-4">Finished Good Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Product</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->product->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="label">Quantity</label>
                            <div class="p-2 bg-base-200 rounded">{{ number_format($good->quantity, 0) }}</div>
                        </div>
                        <div>
                            <label class="label">Batch Number</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->batch_number ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Production Date</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->production_date->format('Y-m-d') }}</div>
                        </div>
                        <div>
                            <label class="label">Purpose</label>
                            <div class="p-2 bg-base-200 rounded">{{ ucfirst(str_replace('_', ' ', $good->purpose)) }}</div>
                        </div>
                        <div>
                            <label class="label">Customer</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->customer->name ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Type</label>
                            <div class="p-2 bg-base-200 rounded">{{ ucfirst($good->type) }}</div>
                        </div>
                        <div>
                            <label class="label">Length (m)</label>
                            <div class="p-2 bg-base-200 rounded">{{ number_format($good->length_m, 2) }}</div>
                        </div>
                        <div>
                            <label class="label">Size</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->size ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Outer Diameter</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->outer_diameter ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Thickness</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->thickness ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Surface</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->surface ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Start Ovality</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->start_ovality ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">End Ovality</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->end_ovality ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Stripe Color</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->stripe_color ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Total Weight (kg)</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->total_weight ? number_format($good->total_weight, 2) : '-' }}</div>
                        </div>
                        <div>
                            <label class="label">Produced By</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->producedBy->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="label">Created At</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                    
                    @if($good->notes)
                        <div class="mt-4">
                            <label class="label">Notes</label>
                            <div class="p-2 bg-base-200 rounded">{{ $good->notes }}</div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="$set('viewingId', null)" class="btn btn-outline">Close</button>
                        <button wire:click="edit({{ $good->id }})" class="btn btn-primary">Edit</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
