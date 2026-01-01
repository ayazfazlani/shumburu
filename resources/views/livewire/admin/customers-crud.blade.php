<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Customers Management</h1>
            <p class="text-gray-500">Create, edit, and delete customers.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search customers..."
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
                New Customer
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
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Code</th>
                    @can('can see customer name')
                    <th class="py-3 px-4">Name</th>
                    @endcan
                    <th class="py-3 px-4">Contact Person</th>
                    <th class="py-3 px-4">Phone</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Address</th>
                    <th class="py-3 px-4">Active</th>
                    <th class="py-3 px-4">Created At</th>
                    <th class="py-3 px-4">Updated At</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="py-3 px-4">{{ $customer->id }}</td>
                        <td class="py-3 px-4">{{ $customer->code }}</td>
                        @can('can see customer name')
                        <td class="py-3 px-4">{{ $customer->name }}</td>
                        @endcan
                        <td class="py-3 px-4">{{ $customer->contact_person ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $customer->phone ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $customer->email ?? '-' }}</td>
                        <td class="py-3 px-4">{{ Str::limit($customer->address ?? '-', 30) }}</td>
                        <td class="py-3 px-4">
                            @if ($customer->is_active)
                                <span class="badge badge-sm badge-success whitespace-nowrap">Yes</span>
                            @else
                                <span class="badge badge-sm badge-error whitespace-nowrap">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $customer->created_at ? $customer->created_at->format('Y-m-d H:i') : '-' }}</td>
                        <td class="py-3 px-4">{{ $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i') : '-' }}</td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button class="btn btn-xs btn-primary"
                                    wire:click="openEditModal({{ $customer->id }})">Edit</button>
                                <button class="btn btn-xs btn-error"
                                    wire:click="confirmDelete({{ $customer->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-gray-400 py-6">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="customer-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveCustomer">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Customer' : 'Create Customer' }}</h3>
            <div class="mb-4">
                <label class="label">Code</label>
                <input type="text" wire:model.defer="code" class="input input-bordered w-full"
                    placeholder="Customer code" />
                @error('code')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full"
                    placeholder="Customer name" />
                @error('name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Contact Person</label>
                <input type="text" wire:model.defer="contact_person" class="input input-bordered w-full"
                    placeholder="Contact person" />
                @error('contact_person')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Phone</label>
                <input type="text" wire:model.defer="phone" class="input input-bordered w-full"
                    placeholder="Phone" />
                @error('phone')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Email</label>
                <input type="email" wire:model.defer="email" class="input input-bordered w-full"
                    placeholder="Email" />
                @error('email')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Address</label>
                <input type="text" wire:model.defer="address" class="input input-bordered w-full"
                    placeholder="Address" />
                @error('address')
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
            <h3 class="font-bold text-lg mb-4">Delete Customer?</h3>
            <p class="mb-4">Are you sure you want to delete this customer? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteCustomer">Delete</button>
            </div>
        </form>
    </dialog>
</section>