<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            Order Items - {{ $productionOrder->order_number ?? 'N/A' }}
        </x-slot:title>
        <x-slot:subtitle>
            @if($productionOrder)
                Customer: {{ $productionOrder->customer->name ?? 'N/A' }}
            @endif
        </x-slot:subtitle>
        <x-slot:buttons>
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Add Item
            </button>
        </x-slot:buttons>
    </x-page-heading>

    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" placeholder="Search by product name or code..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Order Items Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        ID
                        @if($sortField === 'id')
                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                    </th>
                    <th wire:click="sortBy('quantity')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Quantity
                        @if($sortField === 'quantity')
                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unit
                    </th>
                    <th wire:click="sortBy('unit_price')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Unit Price
                        @if($sortField === 'unit_price')
                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('total_price')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Total Price
                        @if($sortField === 'total_price')
                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orderItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->product->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $item->product->code ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->formatted_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->formatted_unit_price }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->formatted_total_price }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button wire:click="edit({{ $item->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $item->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No order items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $orderItems->links() }}
    </div>

    <!-- Total Summary -->
    @if($orderItems->count() > 0)
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-sm font-medium text-gray-700">Total Items:</span>
                    <span class="ml-2 text-sm text-gray-900">{{ $orderItems->total() }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Order Total:</span>
                    <span class="ml-2 text-lg font-bold text-gray-900">
                        {{ number_format($orderItems->sum('total_price'), 2) }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Create/Edit Modal -->
    <dialog id="order-item-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="save">
            <h3 class="font-bold text-lg mb-4">{{ $isEditing ? 'Edit Order Item' : 'Add Order Item' }}</h3>
            
            <!-- Product Selection -->
            <div class="mb-4">
                <label class="label">Product *</label>
                <select wire:model.defer="productId" class="select select-bordered w-full">
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} ({{ $product->code }})
                        </option>
                    @endforeach
                </select>
                @error('productId')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="label">Quantity *</label>
                <input wire:model.live="quantity" type="number" step="0.01" min="0.01"
                       class="input input-bordered w-full" placeholder="Enter quantity" />
                @error('quantity')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Unit -->
            <div class="mb-4">
                <label class="label">Unit *</label>
                <select wire:model.defer="unit" class="select select-bordered w-full">
                    <option value="meter">Meter</option>
                    <option value="roll">Roll</option>
                    <option value="piece">Piece</option>
                    <option value="kg">Kilogram</option>
                </select>
                @error('unit')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Unit Price -->
            <div class="mb-4">
                <label class="label">Unit Price *</label>
                <input wire:model.live="unitPrice" type="number" step="0.01" min="0.01"
                       class="input input-bordered w-full" placeholder="Enter unit price" />
                @error('unitPrice')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <!-- Total Price (Read-only) -->
            <div class="mb-4">
                <label class="label">Total Price</label>
                <input type="text" value="{{ 
                // number_format($totalPrice ?? 0, 2)
                $totalPrice
                 }}" readonly
                       class="input input-bordered w-full bg-base-200" />
            </div>

            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="closeModal">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Order Item?</h3>
            <p class="mb-4">Are you sure you want to delete this order item? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteOrderItem">Delete</button>
            </div>
        </form>
    </dialog>
</section>
