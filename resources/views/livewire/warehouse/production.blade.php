<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Production Entries</h1>
    <button class="btn btn-primary mb-4" wire:click="create">Create Production Entry</button>
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-sm md:text-base">
            <thead>
                <tr>
                    <th>Production Line</th>
                    <th>Product</th>
                    <th>Shift</th>
                    <th>Date</th>
                    {{-- <th>Notes</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productions as $production)
                    <tr>
                        <td class="whitespace-nowrap">{{ $production->productionLine->name ?? '-' }}</td>
                        <td class="whitespace-nowrap">{{ $production->product->name ?? '-' }}</td>
                        <td class="whitespace-nowrap">{{ $production->shift }}</td>
                        <td class="whitespace-nowrap">{{ $production->created_at ? $production->created_at->format('Y-m-d') : '-' }}</td>
                        {{-- <td class="max-w-xs truncate">{{ $production->notes }}</td> --}}
                        <td class="flex flex-col md:flex-row gap-2">
                            <button class="btn btn-sm btn-info" wire:click="edit({{ $production->id }})">Edit</button>
                            <button class="btn btn-sm btn-error" wire:click="delete({{ $production->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-6">No production entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- <div class="mt-4">{{ $productionEntries->links() }}</div> --}}

    <!-- Modal -->
    <dialog id="production-modal" class="modal" @if ($showModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-4xl p-4 md:p-8" wire:submit.prevent="save">
            <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit Production Entry' : 'Create Production Entry' }}</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="label">Production Line</label>
                        <select wire:model="production_line_id" class="input input-bordered w-full">
                            <option value="">Select Line</option>
                            @foreach ($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Product</label>
                        <select wire:model="product_id" class="input input-bordered w-full">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Shift</label>
                        <input type="text" wire:model="shift" class="input input-bordered w-full">
                    </div>
                </div>
                <div>
                    <label class="label">Notes</label>
                    <textarea wire:model="notes" class="input input-bordered w-full"></textarea>
                </div>
                <div>
                    <h2 class="font-semibold mb-2">Raw Materials Used</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Stock Out Batch</th>
                                    <th>Quantity Used</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockOutUsages as $index => $usage)
                                    <tr>
                                        <td>
                                            <select wire:model="stockOutUsages.{{ $index }}.stock_out_line_id" class="input input-bordered">
                                                <option value="">Select Batch</option>
                                                @foreach ($stockOutLines as $line)
                                                    <option value="{{ $line->id }}">
                                                        Batch #{{ $line->materialStockOut->batch_number }} | {{ $line->materialStockOut->rawMaterial->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" min="0.001" step="0.001" wire:model="stockOutUsages.{{ $index }}.quantity_used" class="input input-bordered">
                                        </td>
                                        <td>
                                            <button type="button" wire:click="removeStockOutUsage({{ $index }})" class="btn btn-error btn-sm">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" wire:click="addStockOutUsage" class="btn btn-primary mt-2">Add More</button>
                </div>
                <div>
                    <h2 class="font-semibold mb-2">Finished Goods</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Length (m)</th>
                                    <th>Quantity</th>
                                    <th>Outer Diameter</th>
                                    <th>Size</th>
                                    <th>Surface</th>
                                    <th>Thickness</th>
                                    <th>Ovality</th>
                                    <th>Batch #</th>
                                    <th>Production Date</th>
                                    <th>Purpose</th>
                                    <th>Customer</th>
                                    <th>Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($finishedGoods as $index => $fg)
                                    <tr>
                                        <td>
                                            <select wire:model="finishedGoods.{{ $index }}.type" class="input input-bordered">
                                                <option value="roll">Roll</option>
                                                <option value="cut">Cut</option>
                                            </select>
                                        </td>
                                        <td><input type="number" wire:model="finishedGoods.{{ $index }}.length_m" class="input input-bordered"></td>
                                        <td><input type="number" wire:model="finishedGoods.{{ $index }}.quantity" class="input input-bordered"></td>
                                        <td><input type="number" wire:model="finishedGoods.{{ $index }}.outer_diameter" class="input input-bordered"></td>
                                        <td><input type="text" wire:model="finishedGoods.{{ $index }}.size" class="input input-bordered"></td>
                                        <td><input type="text" wire:model="finishedGoods.{{ $index }}.surface" class="input input-bordered"></td>
                                        <td><input type="number" wire:model="finishedGoods.{{ $index }}.thickness" class="input input-bordered"></td>
                                        <td><input type="number" wire:model="finishedGoods.{{ $index }}.ovality" class="input input-bordered"></td>
                                        <td><input type="text" wire:model="finishedGoods.{{ $index }}.batch_number" class="input input-bordered"></td>
                                        <td><input type="date" wire:model="finishedGoods.{{ $index }}.production_date" class="input input-bordered"></td>
                                        <td>
                                            <select wire:model="finishedGoods.{{ $index }}.purpose" class="input input-bordered">
                                                <option value="for_stock">For Stock</option>
                                                <option value="for_customer_order">For Customer Order</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select wire:model="finishedGoods.{{ $index }}.customer_id" class="input input-bordered">
                                                <option value="">N/A</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" wire:model="finishedGoods.{{ $index }}.notes" class="input input-bordered"></td>
                                        <td>
                                            <button type="button" wire:click="removeFinishedGood({{ $index }})" class="btn btn-error btn-sm">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" wire:click="addFinishedGood" class="btn btn-primary mt-2">Add Finished Good</button>
                </div>
                <div>
                    <h2 class="font-semibold mb-2">Scrap/Waste</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full">
                            <thead>
                                <tr>
                                    <th>Stock Out Batch</th>
                                    <th>Scrap Quantity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockOutScraps as $index => $scrap)
                                    <tr>
                                        <td>
                                            <select wire:model="stockOutScraps.{{ $index }}.stock_out_line_id" class="input input-bordered">
                                                <option value="">Select Batch</option>
                                                @foreach ($stockOutLines as $line)
                                                    <option value="{{ $line->id }}">
                                                        Batch #{{ $line->materialStockOut->batch_number }} | {{ $line->materialStockOut->rawMaterial->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" min="0.001" step="0.001" wire:model="stockOutScraps.{{ $index }}.quantity_scrapped" class="input input-bordered">
                                        </td>
                                        <td>
                                            <button type="button" wire:click="removeStockOutScrap({{ $index }})" class="btn btn-error btn-sm">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" wire:click="addStockOutScrap" class="btn btn-primary mt-2">Add More Scrap</button>
                </div>
                <div class="modal-action flex flex-col md:flex-row gap-2 mt-4">
                    <button type="button" class="btn w-full md:w-auto" wire:click="$set('showModal', false)">Cancel</button>
                    <button type="submit" class="btn btn-success w-full md:w-auto">{{ $isEdit ? 'Update' : 'Save Production Entry' }}</button>
                </div>
            </div>
        </form>
    </dialog>
    @if (session()->has('success'))
        <div class="alert alert-success mt-4">{{ session('success') }}</div>
    @endif
</div>
