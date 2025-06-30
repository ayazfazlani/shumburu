<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Lines Management</h1>
            <p class="text-gray-500">Create, edit, and delete production lines.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search lines..."
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
                New Line
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
                    <th>Min Size</th>
                    <th>Max Size</th>
                    <th>Capacity kg/hr</th>
                    <th>Description</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                    <tr>
                        <td>{{ $line->id }}</td>
                        <td>{{ $line->name }}</td>
                        <td>{{ $line->min_size }}</td>
                        <td>{{ $line->max_size }}</td>
                        <td>{{ $line->capacity_kg_hr }}</td>
                        <td>{{ $line->description }}</td>
                        {{-- <td>
                            @if ($line->is_active)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-error">No</span>
                            @endif
                        </td> --}}
                        <td class="text-right flex space-x-2">
                            <button class="btn btn-sm btn-outline"
                                wire:click="openEditModal({{ $line->id }})">Edit</button>
                            <button class="btn btn-sm btn-error"
                                wire:click="confirmDelete({{ $line->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-6">No lines found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $lines->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="line-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="saveLine">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Line' : 'Create Line' }}</h3>
            <div class="mb-4">
                <label class="label">Name</label>
                <input type="text" wire:model.defer="name" class="input input-bordered w-full"
                    placeholder="Line name" />
                @error('name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Min Size</label>
                <input type="number" wire:model.defer="min_size" class="input input-bordered w-full"
                    placeholder="Min size (e.g. 20)" />
                @error('min_size')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Max Size</label>
                <input type="number" wire:model.defer="max_size" class="input input-bordered w-full"
                    placeholder="Max size (e.g. 110)" />
                @error('max_size')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Capacity kg/hr</label>
                <input type="text" wire:model.defer="capacity_kg_hr" class="input input-bordered w-full"
                    placeholder="Capacity (e.g. 120-150)" />
                @error('capacity_kg_hr')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="mb-4">
                <label class="label">Meter Length</label>
                <input type="number" step="0.01" wire:model.defer="meter_length" class="input input-bordered w-full"
                    placeholder="Meter length" />
                @error('meter_length')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div> --}}
            <div class="mb-4">
                <label class="label">Description</label>
                <textarea wire:model.defer="description" class="textarea textarea-bordered w-full" placeholder="Description"></textarea>
                @error('description')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" wire:model.defer="is_active" class="checkbox checkbox-primary" id="is_active" />
                <label for="is_active" class="label cursor-pointer">Active</label>
                @error('is_active')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div> --}}
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Line?</h3>
            <p class="mb-4">Are you sure you want to delete this line? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deleteLine">Delete</button>
            </div>
        </form>
    </dialog>
</section>
