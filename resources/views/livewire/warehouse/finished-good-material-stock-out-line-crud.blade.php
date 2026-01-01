<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Finished Good Material Stock Out Links</h2>

    <!-- Create/Edit Form -->
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}" class="mb-6 bg-white p-4 rounded shadow">
        <div>
            <label class="block font-semibold mb-1">Finished Good</label>
            <select wire:model="finished_good_id" class="form-select w-full">
                <option value="">Select Finished Good</option>
                @foreach($finishedGoods as $fg)
                    <option value="{{ $fg->id }}">
                        {{ $fg->product->name ?? 'N/A' }} | Type: {{ $fg->type ?? '-' }} | Batch: {{ $fg->batch_number ?? '-' }}
                    </option>
                @endforeach
            </select>
            @error('finished_good_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <h4 class="font-semibold mb-2">Raw Materials Used</h4>
            @foreach($usages as $index => $usage)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <div>
                        <select wire:model="usages.{{ $index }}.material_stock_out_line_id" 
                                wire:change="$refresh" class="form-select w-full">
                            <option value="">Select Material</option>
                            @foreach($stockOutLines as $line)
                                @php
                                    $available = $line->available_quantity ?? 0;
                                @endphp
                                <option value="{{ $line->id }}">
                                    {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}
                                    | Batch: {{ $line->materialStockOut->batch_number ?? '-' }}
                                    | Line: {{ $line->productionLine->name ?? '-' }}
                                    | Consumed: {{ number_format($line->quantity_consumed, 2) }}
                                    | Available: {{ number_format($available, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($usages[$index]['material_stock_out_line_id']) && $usages[$index]['material_stock_out_line_id'])
                            @php
                                $selectedLine = $stockOutLines->firstWhere('id', $usages[$index]['material_stock_out_line_id']);
                                $available = $selectedLine ? $selectedLine->available_quantity : 0;
                            @endphp
                            <span class="text-xs text-blue-600 mt-1 block">
                                Available: {{ number_format($available, 2) }} kg
                            </span>
                        @endif
                        @error('usages.'.$index.'.material_stock_out_line_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="usages.{{ $index }}.quantity_used"
                               wire:change="$refresh"
                               class="form-input w-full @error('usages.' . $index . '.quantity_used') border-red-500 @enderror" 
                               step="0.01" min="0"
                               placeholder="Quantity Used" />
                        <button type="button" wire:click="removeUsageRow({{ $index }})"
                                class="btn btn-xs btn-error">X</button>
                    </div>
                    @error('usages.' . $index . '.quantity_used')
                        <span class="text-red-500 text-xs col-span-2">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach

            <button type="button" wire:click="addUsageRow" class="btn btn-sm btn-secondary mt-2">
                + Add Material
            </button>
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
        @if(session('message'))
            <div class="alert alert-success mb-4">{{ session('message') }}</div>
        @endif
        <table class="table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th class="py-3 px-4">Finished Good</th>
                    <th class="py-3 px-4">Material Stock Out Line</th>
                    <th class="py-3 px-4">Quantity Used</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $link)
                    <tr>
                        <td class="py-3 px-4">
                            @php $fg = $link->finishedGood; @endphp
                            @if($fg)
                                <div><strong>Product:</strong> {{ $fg->product->name ?? 'N/A' }}</div>
                                <div><strong>Type:</strong> {{ $fg->type ?? '-' }}</div>
                                <div><strong>Batch:</strong> {{ $fg->batch_number ?? '-' }}</div>
                            @else
                                #{{ $link->finished_good_id }}
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @php $line = $link->materialStockOutLine; @endphp
                            @if($line)
                                <div><strong>Raw Material:</strong> {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</div>
                                <div><strong>Batch:</strong> {{ $line->materialStockOut->batch_number ?? '-' }}</div>
                                <div><strong>Available:</strong> 
                                    <span class="{{ ($line->available_quantity ?? 0) > 0 ? 'text-blue-600' : 'text-red-600' }} font-semibold">
                                        {{ number_format($line->available_quantity ?? 0, 2) }} kg
                                    </span>
                                </div>
                                <div><strong>Production Line:</strong> {{ $line->productionLine->name ?? '-' }}</div>
                                <div><strong>Qty:</strong> {{ $line->materialStockOut->quantity ?? '-' }}</div>
                            @else
                                #{{ $link->material_stock_out_line_id }}
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $link->quantity_used }}</td>
                        <td class="py-3 px-4">
                            <button wire:click="edit({{ $link->id }})" class="btn btn-xs btn-info">Edit</button>
                            <button wire:click="delete({{ $link->id }})" class="btn btn-xs btn-error ml-2">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $records->links('components.pagination') }}
    </div>
</div>
