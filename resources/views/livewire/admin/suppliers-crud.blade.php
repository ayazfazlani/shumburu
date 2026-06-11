<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Suppliers Management</h1>
            <p class="text-gray-500">Manage raw material suppliers and vendor details.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search suppliers..."
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
                New Supplier
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th class="py-3 px-4">Code</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Contact Person</th>
                    <th class="py-3 px-4">Phone</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Payment Terms</th>
                    <th class="py-3 px-4">Active</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="py-3 px-4 font-mono font-bold">{{ $supplier->code }}</td>
                        <td class="py-3 px-4 font-semibold">{{ $supplier->name }}</td>
                        <td class="py-3 px-4">{{ $supplier->contact_person ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $supplier->phone ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $supplier->email ?? '-' }}</td>
                        <td class="py-3 px-4">
                            @if($supplier->payment_terms)
                                <span class="badge badge-outline badge-sm">{{ $supplier->payment_terms }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if ($supplier->is_active)
                                <span class="badge badge-sm badge-success whitespace-nowrap">Active</span>
                            @else
                                <span class="badge badge-sm badge-error whitespace-nowrap">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button class="btn btn-xs btn-primary"
                                    wire:click="openEditModal({{ $supplier->id }})">Edit</button>
                                <button class="btn btn-xs btn-error"
                                    wire:click="confirmDelete({{ $supplier->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-400 py-6">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $suppliers->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="supplier-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveSupplier">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Supplier' : 'Create Supplier' }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="label">Code <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="code" class="input input-bordered w-full" placeholder="e.g. SUP-001" />
                    @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="label">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="name" class="input input-bordered w-full" placeholder="Company name" />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="label">Contact Person</label>
                    <input type="text" wire:model.defer="contact_person" class="input input-bordered w-full" placeholder="Contact name" />
                    @error('contact_person') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="label">Phone</label>
                    <input type="text" wire:model.defer="phone" class="input input-bordered w-full" placeholder="+92 300 1234567" />
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="label">Email</label>
                    <input type="email" wire:model.defer="email" class="input input-bordered w-full" placeholder="supplier@company.com" />
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="label">Payment Terms</label>
                    <input type="text" wire:model.defer="payment_terms" class="input input-bordered w-full" placeholder="e.g. Net 30, Cash on Delivery" />
                    @error('payment_terms') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mb-4">
                <label class="label">Address</label>
                <input type="text" wire:model.defer="address" class="input input-bordered w-full" placeholder="Supplier address" />
                @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" wire:model.defer="is_active" class="checkbox checkbox-primary" id="sup_is_active" />
                <label for="sup_is_active" class="label cursor-pointer">Active</label>
            </div>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-supplier-modal" class="modal" @if ($showDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Supplier?</h3>
            <p class="mb-4">Are you sure you want to delete this supplier? This cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteSupplier">Delete</button>
            </div>
        </form>
    </dialog>
</section>
