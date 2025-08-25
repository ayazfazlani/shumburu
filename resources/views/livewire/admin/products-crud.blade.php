<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Products Management</h1>
            <p class="text-gray-500">Create, edit, and delete products.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search products..."
                class="input input-bordered" />
            <select wire:model="perPage" class="select select-bordered">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <button class="btn btn-primary" wire:click="openCreateModal">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Product
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="w-64">Name</th>
                    <th>Code</th>
                    <th>Size</th>
                    <th>PN</th>
                    <th>Weight per Meter</th>
                    <th>Meter Length</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td colspan="3" class="whitespace-nowrap overflow-hidden">
                        {{ $product->name }}
                        </td>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->size }}</td>
                        <td>{{ $product->pn }}</td>
                        <td>{{ $product->weight_per_meter }}</td>
                        <td>{{ $product->meter_length }}</td>
                        <td>{{ $product->description }}</td>
                        <td>
                            @if ($product->is_active)
                                <span class="badge badge-sm badge-success">Yes</span>
                            @else
                                <span class="badge badge-sm badge-error">No</span>
                            @endif
                        </td>
                        <td class="text-right flex space-x-2">
                            <button class="btn btn-xs btn-outline"
                                wire:click="openEditModal({{ $product->id }})">Edit</button>
                            <button class="btn btn-xs btn-error"
                                wire:click="confirmDelete({{ $product->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-400 py-6">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="product-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveProduct">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Product' : 'Create Product' }}</h3>
            <div class="mb-4">
                <label class="label">Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full"
                    placeholder="Product name" />
                @error('name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Code</label>
                <input type="text" wire:model.defer="code" class="input input-bordered w-full"
                    placeholder="Product code" />
                @error('code')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Size</label>
                <input type="text" wire:model.defer="size" class="input input-bordered w-full"
                    placeholder="Size (e.g. 20mm, 32mm)" />
                @error('size')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">PN</label>
                <input type="text" wire:model.defer="pn" class="input input-bordered w-full"
                    placeholder="PN (e.g. PN6, PN10)" />
                @error('pn')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Weight Per Meter</label>
                <input type="text" wire:model.defer="WeightPerMeter" class="input input-bordered w-full"
                    placeholder="KG (e.g. 1Kg, !0kg)" />
                @error('WeightPerMeter')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Meter Length</label>
                <input type="number" step="0.01" wire:model.defer="meter_length" class="input input-bordered w-full"
                    placeholder="Meter length" />
                @error('meter_length')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Description</label>
                <textarea wire:model.defer="description" class="textarea textarea-bordered w-full" placeholder="Description"></textarea>
                @error('description')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" wire:model.defer="is_active" class="checkbox checkbox-primary" id="is_active" />
                <label for="is_active" class="label cursor-pointer">Active</label>
                @error('is_active')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Product?</h3>
            <p class="mb-4">Are you sure you want to delete this product? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteProduct">Delete</button>
            </div>
        </form>
    </dialog>
</section>
