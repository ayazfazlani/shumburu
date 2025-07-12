<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Material Stock Out Lines</h2>

    <!-- Create/Edit Form -->
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="mb-6 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Stock Out Batch</label>
                <select wire:model="material_stock_out_id" class="form-select w-full">
                    <option value="">Select Stock Out Batch</option>
                    @foreach($stockOuts as $stockOut)
                        <option value="{{ $stockOut->id }}">
                            #{{ $stockOut->id }} - {{ $stockOut->rawMaterial->name ?? 'N/A' }} | Batch: {{ $stockOut->batch_number ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('material_stock_out_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Production Line</label>
                <select wire:model="production_line_id" class="form-select w-full">
                    <option value="">Select Production Line</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->name ?? ('Line #' . $line->id) }}</option>
                    @endforeach
                </select>
                @error('production_line_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-semibold mb-1">Quantity Consumed (kg)</label>
                <input type="number" wire:model="quantity_consumed" class="form-input w-full" step="0.01" min="0.01" />
                @error('quantity_consumed') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
            @if($isEdit)
                <button type="button" wire:click="$set('isEdit', false)" class="btn btn-secondary">Cancel</button>
            @endif
        </div>
    </form>
    <div class="mt-8">
        <h3 class="text-lg font-bold mb-2">Material Stock Out Lines</h3>
        <table class="table-auto w-full text-sm">
            <thead>
                <tr>
                    <th class="px-2 py-1">Raw Material</th>
                    <th class="px-2 py-1">Batch</th>
                    <th class="px-2 py-1">Production Line</th>
                    <th class="px-2 py-1">Quantity Consumed</th>
                    <th class="px-2 py-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materialStockOutLines as $line)
                    <tr>
                        <td>{{ $line->materialStockOut && $line->materialStockOut->rawMaterial ? $line->materialStockOut->rawMaterial->name : 'N/A' }}</td>
                        <td>{{ $line->materialStockOut ? $line->materialStockOut->batch_number : '-' }}</td>
                        <td>{{ $line->productionLine ? $line->productionLine->name : '-' }}</td>
                        <td>{{ $line->quantity_consumed }}</td>
                        <td>
                            <button wire:click="edit({{ $line->id }})" class="btn btn-xs btn-info">Edit</button>
                            <button wire:click="delete({{ $line->id }})" class="btn btn-xs btn-error ml-2">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 