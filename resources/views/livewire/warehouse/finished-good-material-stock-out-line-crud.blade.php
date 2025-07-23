<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Finished Good Material Stock Out Links</h2>

    <!-- Create/Edit Form -->
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="mb-6 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Finished Good</label>
                <select wire:model="finished_good_id" class="form-select w-full">
                    <option value="">Select Finished Good</option>
                    @foreach($finishedGoods as $fg)
                        <option value="{{ $fg->id }}">
                            #{{ $fg->id }} - {{ $fg->product->name ?? 'N/A' }} | Type: {{ $fg->type ?? '-' }} | Batch: {{ $fg->batch_number ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('finished_good_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-1">Material Stock Out Line</label>
                <select wire:model="material_stock_out_line_id" class="form-select w-full">
                    <option value="">Select Stock Out Line</option>
                    @foreach($stockOutLines as $line)
                        <option value="{{ $line->id }}">
                            #{{ $line->id }} - {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }} | Batch: {{ $line->materialStockOut->batch_number ?? '-' }} | Line: {{ $line->productionLine->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('material_stock_out_line_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-semibold mb-1">Quantity Used</label>
                <input type="number" wire:model="quantity_used" class="form-input w-full" step="0.01" min="0" />
                @error('quantity_used') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
        <h3 class="text-lg font-bold mb-2">Links</h3>
        <table class="table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th class="p-2 py-1">Finished Good</th>
                    <th class="px-2 py-1">Material Stock Out Line</th>
                    <th class="px-2 py-1">Quantity Used</th>
                    <th class="px-2 py-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($links as $link)
                    <tr>
                        <td>
                            @php $fg = $link->finishedGood; @endphp
                            @if($fg)
                                <div><strong>Product:</strong> {{ $fg->product->name ?? 'N/A' }}</div>
                                <div><strong>Type:</strong> {{ $fg->type ?? '-' }}</div>
                                <div><strong>Batch:</strong> {{ $fg->batch_number ?? '-' }}</div>
                            @else
                                #{{ $link->finished_good_id }}
                            @endif
                        </td>
                        <td>
                            @php $line = $link->materialStockOutLine; @endphp
                            @if($line)
                                <div><strong>Raw Material:</strong> {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</div>
                                <div><strong>Batch:</strong> {{ $line->materialStockOut->batch_number ?? '-' }}</div>
                                <div><strong>Production Line:</strong> {{ $line->productionLine->name ?? '-' }}</div>
                            @else
                                #{{ $link->material_stock_out_line_id }}
                            @endif
                        </td>
                        <td>{{ $link->quantity_used }}</td>
                        <td>
                            <button wire:click="edit({{ $link->id }})" class="btn btn-xs btn-info">Edit</button>
                            <button wire:click="delete({{ $link->id }})" class="btn btn-xs btn-error ml-2">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 