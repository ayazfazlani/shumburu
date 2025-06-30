<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Raw Materials Management</h1>
            <p class="text-gray-500">Create, edit, and delete raw materials.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search raw materials..."
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
                New Raw Material
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
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Active</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rawMaterials as $rawMaterial)
                    <tr>
                        <td>{{ $rawMaterial->id }}</td>
                        <td>{{ $rawMaterial->name }}</td>
                        <td>{{ $rawMaterial->code }}</td>
                        <td>{{ $rawMaterial->description }}</td>
                        <td>{{ $rawMaterial->unit }}</td>
                        <td>
                            @if ($rawMaterial->is_active)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-error">No</span>
                            @endif
                        </td>
                        <td class="text-right flex space-x-2">
                            <button class="btn btn-sm btn-outline"
                                wire:click="openEditModal({{ $rawMaterial->id }})">Edit</button>
                            <button class="btn btn-sm btn-error"
                                wire:click="confirmDelete({{ $rawMaterial->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-6">No raw materials found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rawMaterials->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="raw-material-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveRawMaterial">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Raw Material' : 'Create Raw Material' }}</h3>
            <div class="mb-4">
                <label class="label">Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full"
                    placeholder="Raw material name" />
                @error('name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Code</label>
                <input type="text" wire:model.defer="code" class="input input-bordered w-full"
                    placeholder="Raw material code" />
                @error('code')
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
            <div class="mb-4">
                <label class="label">Unit</label>
                <input type="text" wire:model.defer="unit" class="input input-bordered w-full"
                    placeholder="Unit (e.g. kg, ton, bag)" />
                @error('unit')
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
            <h3 class="font-bold text-lg mb-4">Delete Raw Material?</h3>
            <p class="mb-4">Are you sure you want to delete this raw material? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteRawMaterial">Delete</button>
            </div>
        </form>
    </dialog>
</section>
