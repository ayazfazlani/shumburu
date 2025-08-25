<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Users Management</h1>
            <p class="text-gray-500">Create, edit, assign roles, and delete users.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search users..."
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
                New User
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
                    <th>Email</th>
                    <th>Roles</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->roles as $role)
                                    <span class="badge badge-outline badge-sm">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-right flex space-x-2">
                            <button class="btn btn-xs btn-outline"
                                wire:click="openEditModal({{ $user->id }})">Edit</button>
                            <button class="btn btn-xs btn-error"
                                wire:click="confirmDelete({{ $user->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-6">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="user-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveUser">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit User Roles' : 'Create User' }}</h3>
            @if(!$isEdit)
            <div class="mb-4">
                <label class="label">Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full" placeholder="Name" />
                @error('name')
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
                <label class="label">Password</label>
                <input type="password" wire:model.defer="password" class="input input-bordered w-full"
                    placeholder="Password" />
                @if (!$isEdit)
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                @endif
            </div>
            <div class="mb-4">
                <label class="label">Confirm Password</label>
                <input type="password" wire:model.defer="password_confirmation" class="input input-bordered w-full"
                    placeholder="Confirm Password" />
            </div>
            @endif
            <div class="mb-4">
                <label class="label">Roles</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($roles as $role)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model.defer="selectedRoles" value="{{ $role->name }}"
                                class="checkbox checkbox-sm" />
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
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
            <h3 class="font-bold text-lg mb-4">Delete User?</h3>
            <p class="mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteUser">Delete</button>
            </div>
        </form>
    </dialog>
</section>
