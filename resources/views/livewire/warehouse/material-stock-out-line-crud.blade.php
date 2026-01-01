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
                        <select wire:model="materials.{{ $index }}.material_stock_out_id" 
                                wire:change="$refresh" class="form-select w-full">
                            <option value="">Select Material</option>
                            @foreach($stockOuts as $stockOut)
                                @php
                                    $available = $this->getAvailableQuantity($stockOut->id);
                                @endphp
                                <option value="{{ $stockOut->id }}">
                                    {{ $stockOut->rawMaterial->name ?? 'N/A' }} 
                                    | Batch: {{ $stockOut->batch_number ?? '-' }} 
                                    | Stocked: {{ round($stockOut->quantity, 2) }}
                                    | Available: {{ round($available, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($materials[$index]['material_stock_out_id']) && $materials[$index]['material_stock_out_id'])
                            @php
                                $available = $this->getAvailableQuantity($materials[$index]['material_stock_out_id']);
                            @endphp
                            <span class="text-xs text-blue-600 mt-1 block">
                                Available: {{ round($available, 2) }} kg
                            </span>
                        @endif
                        @error('materials.' . $index . '.material_stock_out_id') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <input type="number" wire:model="materials.{{ $index }}.quantity_consumed" 
                               wire:change="$refresh"
                               placeholder="Quantity (kg)" step="0.01" min="0.01" 
                               class="form-input w-full @error('materials.' . $index . '.quantity_consumed') border-red-500 @enderror" />
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
        @if(session('message'))
            <div class="alert alert-success mb-4">{{ session('message') }}</div>
        @endif
        <table class="table-zebra w-full">
            <thead class="bg-gray-50">
                <tr>    
                    <th class="py-3 px-4">Raw Material</th>
                    <th class="py-3 px-4">Batch</th>
                    <th class="py-3 px-4">Production Line</th>
                    <th class="py-3 px-4">Quantity Consumed</th>
                    <th class="py-3 px-4">Used</th>
                    <th class="py-3 px-4">Returned</th>
                    <th class="py-3 px-4">Available</th>
                    <th class="py-3 px-4">Shift</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materialStockOutLines as $line)
                    @php
                        $used = $line->total_used_quantity ?? 0;
                        $returned = $line->quantity_returned ?? 0;
                        $available = $line->available_quantity ?? 0;
                    @endphp
                    <tr>
                        <td class="py-3 px-4">{{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4">{{ $line->materialStockOut->batch_number ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $line->productionLine->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ number_format($line->quantity_consumed, 2) }}</td>
                        <td class="py-3 px-4">
                            <span class="text-orange-600 font-semibold">{{ number_format($used, 2) }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($returned > 0)
                                <span class="text-green-600 font-semibold">{{ number_format($returned, 2) }}</span>
                                @if($line->returned_at)
                                    <br><span class="text-xs text-gray-500">Returned: {{ $line->returned_at->format('Y-m-d') }}</span>
                                @endif
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold {{ $available > 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($available, 2) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">{{ $line->shift }}</td>
                        <td class="py-3 px-4">
                            <div class="flex gap-1">
                                @if($available > 0)
                                    <button wire:click="openReturnModal({{ $line->id }})" 
                                            class="btn btn-xs btn-success">Return</button>
                                @endif
                                <button wire:click="delete({{ $line->id }})" 
                                        class="btn btn-xs btn-error" 
                                        onclick="return confirm('Are you sure?')">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Return Modal -->
    @if($showReturnModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeReturnModal">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4" wire:click.stop>
            <h3 class="text-lg font-bold mb-4">Return Stock</h3>
            
            @if($returnLineId)
                @php
                    $line = \App\Models\MaterialStockOutLine::find($returnLineId);
                    $available = $line ? $line->available_quantity : 0;
                @endphp
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <strong>Material:</strong> {{ $line->materialStockOut->rawMaterial->name ?? 'N/A' }}<br>
                        <strong>Batch:</strong> {{ $line->materialStockOut->batch_number ?? '-' }}<br>
                        <strong>Available to Return:</strong> <span class="text-blue-600 font-semibold">{{ number_format($available, 2) }} kg</span>
                    </p>
                </div>
            @endif

            <div class="mb-4">
                <label class="block font-semibold mb-1">Return Quantity (kg) <span class="text-red-500">*</span></label>
                <input type="number" wire:model="returnQuantity" 
                       step="0.01" min="0.01" 
                       max="{{ $available ?? 0 }}"
                       class="input input-bordered w-full @error('returnQuantity') input-error @enderror" 
                       placeholder="Enter quantity to return" />
                @error('returnQuantity')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Return Notes</label>
                <textarea wire:model="returnNotes" 
                          class="textarea textarea-bordered w-full" 
                          rows="3" 
                          placeholder="Optional notes about the return"></textarea>
            </div>

            <div class="flex gap-2 justify-end">
                <button wire:click="closeReturnModal" class="btn btn-ghost">Cancel</button>
                <button wire:click="processReturn" class="btn btn-success">
                    Process Return
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
