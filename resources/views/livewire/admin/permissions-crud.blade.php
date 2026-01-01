<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Permissions Management</h1>
            <p class="text-gray-500">Create, edit, and delete permissions.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search permissions..."
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
                New Permission
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
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td class="py-3 px-4">{{ $permission->id }}</td>
                        <td class="py-3 px-4">{{ $permission->name }}</td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <button class="btn btn-xs btn-primary"
                                    wire:click="openEditModal({{ $permission->id }})">Edit</button>
                                <button class="btn btn-xs btn-error"
                                    wire:click="confirmDelete({{ $permission->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-400 py-6">No permissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $permissions->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="permission-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-md" wire:submit.prevent="savePermission">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Permission' : 'Create Permission' }}</h3>
            <div class="mb-4">
                <label class="label">Permission Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full"
                    placeholder="Permission name" />
                @error('name')
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
            <h3 class="font-bold text-lg mb-4">Delete Permission?</h3>
            <p class="mb-4">Are you sure you want to delete this permission? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deletePermission">Delete</button>
            </div>
        </form>
    </dialog>
</section>
