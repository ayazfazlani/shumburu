<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Material Stock Out Lines (Batch Entry)</h2>

    <!-- Batch Create Form -->
    <form wire:submit.prevent="saveBatch" class="mb-6 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <div>
                <label class="block font-semibold mb-1">Shift</label>
                <select wire:model="shift" class="form-select w-full">
                    <option value="">Select Shift</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
                @error('shift') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-4">
            <h3 class="font-bold mb-2">Materials</h3>
            @foreach($materials as $index => $material)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                    <div>
                        <select wire:model="materials.{{ $index }}.material_stock_out_id" class="form-select w-full">
                            <option value="">Select Material</option>
                            @foreach($stockOuts as $stockOut)
                                <option value="{{ $stockOut->id }}">
                                    {{ $stockOut->rawMaterial->name ?? 'N/A' }} 
                                    | Batch: {{ $stockOut->batch_number ?? '-' }} 
                                    | Qty: {{ round($stockOut->quantity) ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('materials.' . $index . '.material_stock_out_id') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <input type="number" wire:model="materials.{{ $index }}.quantity_consumed" 
                               placeholder="Quantity (kg)" step="0.01" min="0.01" 
                               class="form-input w-full" />
                        @error('materials.' . $index . '.quantity_consumed') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="flex items-center">
                        @if($index > 0)
                            <button type="button" wire:click="removeRow({{ $index }})" 
                                    class="btn btn-error btn-sm">Remove</button>
                        @endif
                    </div>
                </div>
            @endforeach

            <button type="button" wire:click="addRow" class="btn btn-secondary btn-sm mt-2">+ Add Material</button>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save Batch</button>
        </div>
    </form>

    <div class="mt-8">
        <h3 class="text-lg font-bold mb-2">Recorded Stock Out Lines</h3>
        <table class="table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th>Raw Material</th>
                    <th>Batch</th>
                    <th>Production Line</th>
                    <th>Quantity Consumed</th>
                    <th>Shift</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materialStockOutLines as $line)
                    <tr>
                        <td>{{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</td>
                        <td>{{ $line->materialStockOut->batch_number ?? '-' }}</td>
                        <td>{{ $line->productionLine->name ?? '-' }}</td>
                        <td>{{ $line->quantity_consumed }}</td>
                        <td>{{ $line->shift }}</td>
                        <td>
                            <button wire:click="delete({{ $line->id }})" class="btn btn-xs btn-error ml-2">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
