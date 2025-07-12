<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Scrap/Waste Records</h2>

    <!-- Create/Edit Form -->
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="mb-6 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Material Stock Out Line</label>
                <input type="number" wire:model="material_stock_out_line_id" class="form-input w-full" />
                @error('material_stock_out_line_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Reason</label>
                <input type="text" wire:model="reason" class="form-input w-full" />
                @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Waste Date</label>
                <input type="date" wire:model="waste_date" class="form-input w-full" />
                @error('waste_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Recorded By (User ID)</label>
                <input type="number" wire:model="recorded_by" class="form-input w-full" />
                @error('recorded_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Notes</label>
                <textarea wire:model="notes" class="form-input w-full"></textarea>
                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
            @if($isEdit)
                <button type="button" wire:click="$set('isEdit', false)" class="btn btn-secondary ml-2">Cancel</button>
            @endif
        </div>
    </form>

    <!-- Table of Scrap/Waste Records -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Stock Out Line</th>
                    <th class="px-4 py-2">Reason</th>
                    <th class="px-4 py-2">Waste Date</th>
                    <th class="px-4 py-2">Recorded By</th>
                    <th class="px-4 py-2">Notes</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scrapWastes as $sw)
                    <tr>
                        <td class="border px-4 py-2">{{ $sw->id }}</td>
                        <td class="border px-4 py-2">#{{ $sw->material_stock_out_line_id }}</td>
                        <td class="border px-4 py-2">{{ $sw->reason }}</td>
                        <td class="border px-4 py-2">{{ $sw->waste_date }}</td>
                        <td class="border px-4 py-2">{{ $sw->recorded_by }}</td>
                        <td class="border px-4 py-2">{{ $sw->notes }}</td>
                        <td class="border px-4 py-2">
                            <button wire:click="edit({{ $sw->id }})" class="btn btn-sm btn-info">Edit</button>
                            <button wire:click="delete({{ $sw->id }})" class="btn btn-sm btn-danger ml-2">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No scrap/waste records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div> 